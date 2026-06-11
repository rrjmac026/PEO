<?php

namespace App\Services;

use App\Models\DataBackup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class BackupService
{
    protected string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');

        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Create a database-only backup
    // ─────────────────────────────────────────────────────────────
    public function createDatabaseBackup(int $userId, int $retentionDays = 30): DataBackup
    {
        $backupName = 'peo-db-backup-' . now()->format('Y-m-d-His');

        $backup = DataBackup::create([
            'backup_name'     => $backupName,
            'backup_type'     => 'database',
            'status'          => 'processing',
            'created_by'      => $userId,
            'backup_date'     => now(),
            'retention_until' => now()->addDays($retentionDays),
            'progress'        => 0,
        ]);

        try {
            $sqlFile = $this->backupPath . '/' . $backupName . '.sql';
            $zipFile = $this->backupPath . '/' . $backupName . '.zip';

            // Dump the database
            $this->dumpDatabase($sqlFile);

            $backup->update(['progress' => 70]);

            // Zip it up
            $this->zipFile($sqlFile, $zipFile);

            // Clean up raw SQL
            if (File::exists($sqlFile)) {
                File::delete($sqlFile);
            }

            $backup->update([
                'status'       => 'completed',
                'file_path'    => 'backups/' . $backupName . '.zip',
                'file_size'    => File::size($zipFile),
                'completed_at' => now(),
                'progress'     => 100,
            ]);

        } catch (\Throwable $e) {
            Log::error('BackupService: database backup failed', [
                'backup_id' => $backup->id,
                'error'     => $e->getMessage(),
            ]);

            $backup->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'progress'      => 0,
            ]);
        }

        return $backup->fresh();
    }

    // ─────────────────────────────────────────────────────────────
    // Create a full backup (DB + storage)
    // ─────────────────────────────────────────────────────────────
    public function createFullBackup(int $userId, int $retentionDays = 30): DataBackup
    {
        $backupName = 'peo-full-backup-' . now()->format('Y-m-d-His');

        $backup = DataBackup::create([
            'backup_name'     => $backupName,
            'backup_type'     => 'full',
            'status'          => 'processing',
            'created_by'      => $userId,
            'backup_date'     => now(),
            'retention_until' => now()->addDays($retentionDays),
            'progress'        => 0,
        ]);

        try {
            $sqlFile = $this->backupPath . '/' . $backupName . '.sql';
            $zipFile = $this->backupPath . '/' . $backupName . '.zip';

            // Dump DB
            $this->dumpDatabase($sqlFile);

            $backup->update(['progress' => 40]);

            // Zip DB + storage folder
            $this->zipFull($sqlFile, $zipFile);

            if (File::exists($sqlFile)) {
                File::delete($sqlFile);
            }

            $backup->update([
                'status'       => 'completed',
                'file_path'    => 'backups/' . $backupName . '.zip',
                'file_size'    => File::size($zipFile),
                'completed_at' => now(),
                'progress'     => 100,
            ]);

        } catch (\Throwable $e) {
            Log::error('BackupService: full backup failed', [
                'backup_id' => $backup->id,
                'error'     => $e->getMessage(),
            ]);

            $backup->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'progress'      => 0,
            ]);
        }

        return $backup->fresh();
    }

    // ─────────────────────────────────────────────────────────────
    // Delete a backup record + its file
    // ─────────────────────────────────────────────────────────────
    public function deleteBackup(DataBackup $backup): void
    {
        if ($backup->file_path) {
            $fullPath = storage_path('app/' . $backup->file_path);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }

        $backup->delete();
    }

    // ─────────────────────────────────────────────────────────────
    // Clean expired backups (past retention_until date)
    // ─────────────────────────────────────────────────────────────
    public function cleanExpiredBackups(): int
    {
        $expired = DataBackup::where('retention_until', '<=', now())
            ->where('status', 'completed')
            ->get();

        $count = 0;
        foreach ($expired as $backup) {
            try {
                $this->deleteBackup($backup);
                $count++;
            } catch (\Throwable $e) {
                Log::warning('BackupService: failed to delete expired backup', [
                    'backup_id' => $backup->id,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        return $count;
    }

    // ─────────────────────────────────────────────────────────────
    // Private: dump database to SQL file
    // Uses mysqldump if available, falls back to PHP PDO export
    // ─────────────────────────────────────────────────────────────
    protected function dumpDatabase(string $outputPath): void
    {
        $connection = config('database.default');
        $config     = config("database.connections.{$connection}");

        if ($connection !== 'mysql') {
            throw new \RuntimeException("BackupService only supports MySQL/MariaDB. Current driver: {$connection}");
        }

        // Try mysqldump first
        $returnCode = null;
        exec('mysqldump --version 2>&1', $out, $returnCode);

        if ($returnCode === 0) {
            $this->dumpViaMysqldump($config, $outputPath);
        } else {
            // PHP fallback
            $this->dumpViaPdo($config, $outputPath);
        }
    }

    protected function dumpViaMysqldump(array $config, string $outputPath): void
    {
        $host     = escapeshellarg($config['host']     ?? '127.0.0.1');
        $port     = escapeshellarg($config['port']     ?? '3306');
        $database = escapeshellarg($config['database']);
        $username = escapeshellarg($config['username']);
        $password = $config['password'] ?? '';

        // Write password to a temp option file so it never appears in process list
        $optFile = tempnam(sys_get_temp_dir(), 'mysql_opt_');
        File::put($optFile, "[client]\npassword=" . addslashes($password) . "\n");

        $cmd = sprintf(
            'mysqldump --defaults-extra-file=%s -h %s -P %s -u %s --single-transaction --routines --triggers %s > %s 2>&1',
            escapeshellarg($optFile),
            $host,
            $port,
            $username,
            $database,
            escapeshellarg($outputPath)
        );

        $returnCode = null;
        exec($cmd, $output, $returnCode);

        File::delete($optFile);

        if ($returnCode !== 0) {
            throw new \RuntimeException('mysqldump failed: ' . implode("\n", $output));
        }
    }

    protected function dumpViaPdo(array $config, string $outputPath): void
    {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
        $pdo = new \PDO($dsn, $config['username'], $config['password']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $handle = fopen($outputPath, 'w');
        if (!$handle) {
            throw new \RuntimeException("Cannot write to: {$outputPath}");
        }

        fwrite($handle, "-- PEO Database Backup\n");
        fwrite($handle, "-- Generated: " . now()->toDateTimeString() . "\n");
        fwrite($handle, "-- Database: {$config['database']}\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        // Get all tables
        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // DROP + CREATE
            $createRow = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
            $createSql = $createRow['Create Table'] ?? $createRow[array_key_last($createRow)];

            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($handle, $createSql . ";\n\n");

            // INSERT rows in chunks to avoid memory issues
            $stmt = $pdo->query("SELECT * FROM `{$table}`");

            $rows = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $values = array_map(
                    fn ($v) => is_null($v) ? 'NULL' : $pdo->quote((string) $v),
                    array_values($row)
                );
                $rows[] = '(' . implode(', ', $values) . ')';

                // Flush every 500 rows
                if (count($rows) >= 500) {
                    fwrite($handle, "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $rows) . ";\n\n");
                    $rows = [];
                }
            }

            if (!empty($rows)) {
                fwrite($handle, "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $rows) . ";\n\n");
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    // ─────────────────────────────────────────────────────────────
    // Private: zip helpers
    // ─────────────────────────────────────────────────────────────
    protected function zipFile(string $sourceFile, string $zipPath): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create zip: {$zipPath}");
        }

        $zip->addFile($sourceFile, basename($sourceFile));
        $zip->close();
    }

    protected function zipFull(string $sqlFile, string $zipPath): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create zip: {$zipPath}");
        }

        // Add SQL dump
        $zip->addFile($sqlFile, 'database/' . basename($sqlFile));

        // Add storage/app/public recursively (skip the backups folder itself)
        $storagePath = storage_path('app/public');
        if (File::exists($storagePath)) {
            $this->addDirectoryToZip($zip, $storagePath, 'storage/public');
        }

        $zip->close();
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $dirPath, string $zipPrefix): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dirPath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isFile()) continue;

            $realPath    = $file->getRealPath();
            $relativePath = $zipPrefix . '/' . substr($realPath, strlen($dirPath) + 1);

            $zip->addFile($realPath, $relativePath);
        }
    }
}