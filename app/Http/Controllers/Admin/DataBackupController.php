<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataBackup;
use App\Services\BackupService;
use App\Jobs\CreateDatabaseBackupJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class DataBackupController extends Controller
{
    public function __construct(protected BackupService $backupService) {}

    // ─────────────────────────────────────────────────────────────
    // GET /admin/backups
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = DataBackup::with('creator')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('backup_type', $request->type);
        }

        $backups = $query->paginate(15)->withQueryString();
        $stats   = $this->getStats();

        return view('admin.backups.index', compact('backups', 'stats'));
    }

    // ─────────────────────────────────────────────────────────────
    // POST /admin/backups
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'backup_type'    => 'nullable|in:database,full',
            'retention_days' => 'nullable|integer|min:1|max:365',
            'async'          => 'nullable|boolean',
        ]);

        $backupType    = $validated['backup_type']    ?? 'database';
        $retentionDays = $validated['retention_days'] ?? 30;
        $async         = $validated['async']          ?? false;

        try {
            // Ensure backup directory exists
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // Queue it if async + queue driver is not sync
            if ($async && config('queue.default') !== 'sync') {
                CreateDatabaseBackupJob::dispatch(Auth::id(), $backupType, $retentionDays);

                return back()->with('success', 'Backup queued! It will run in the background.');
            }

            // Synchronous
            $backup = $backupType === 'full'
                ? $this->backupService->createFullBackup(Auth::id(), $retentionDays)
                : $this->backupService->createDatabaseBackup(Auth::id(), $retentionDays);

            if ($backup->status === 'completed') {
                return back()->with('success', "Backup completed! Size: {$backup->formatted_size}");
            }

            return back()->with('error', 'Backup failed: ' . ($backup->error_message ?? 'Unknown error'));

        } catch (\Exception $e) {
            Log::error('DataBackupController@store failed', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage(),
            ]);

            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────
    // GET /admin/backups/{backup}/download
    // ─────────────────────────────────────────────────────────────
    public function download(DataBackup $backup)
    {
        if (!$backup->canDownload()) {
            return back()->with('error', 'This backup is not available for download.');
        }

        $filePath = storage_path('app/' . $backup->file_path);

        if (!File::exists($filePath)) {
            Log::error('Backup file missing on disk', [
                'backup_id' => $backup->id,
                'path'      => $filePath,
            ]);
            return back()->with('error', 'Backup file not found on disk.');
        }

        return response()->download($filePath, $backup->backup_name . '.zip', [
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $backup->backup_name . '.zip"',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // DELETE /admin/backups/{backup}
    // ─────────────────────────────────────────────────────────────
    public function destroy(DataBackup $backup)
    {
        if (!$backup->canDelete()) {
            return back()->with('error', 'Cannot delete a backup that is still processing.');
        }

        try {
            $this->backupService->deleteBackup($backup);
            return back()->with('success', 'Backup deleted successfully.');
        } catch (\Exception $e) {
            Log::error('DataBackupController@destroy failed', [
                'backup_id' => $backup->id,
                'error'     => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────
    // POST /admin/backups/cleanup
    // ─────────────────────────────────────────────────────────────
    public function cleanup()
    {
        try {
            $count = $this->backupService->cleanExpiredBackups();
            return back()->with('success', "Cleaned up {$count} expired backup(s).");
        } catch (\Exception $e) {
            Log::error('DataBackupController@cleanup failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Cleanup failed: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────
    // GET /admin/backups/{backup}/status  (JSON — AJAX polling)
    // ─────────────────────────────────────────────────────────────
    public function status(DataBackup $backup)
    {
        return response()->json([
            'id'             => $backup->id,
            'status'         => $backup->status,
            'progress'       => $backup->progress ?? 0,
            'error_message'  => $backup->error_message,
            'formatted_size' => $backup->formatted_size,
            'completed_at'   => $backup->completed_at?->toIso8601String(),
            'can_download'   => $backup->canDownload(),
            'can_delete'     => $backup->canDelete(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // GET /admin/backups/test  (JSON — system diagnostics)
    // ─────────────────────────────────────────────────────────────
    public function test()
    {
        $results = ['timestamp' => now()->toDateTimeString(), 'tests' => []];

        // Test 1: DB connection
        try {
            $connection = config('database.default');
            $config     = config("database.connections.{$connection}");
            \DB::connection()->getPdo();
            $results['tests']['database'] = [
                'status'   => 'success',
                'driver'   => $config['driver'],
                'database' => $config['database'],
            ];
        } catch (\Exception $e) {
            $results['tests']['database'] = ['status' => 'failed', 'error' => $e->getMessage()];
        }

        // Test 2: Backup directory
        $backupDir = storage_path('app/backups');
        try {
            if (!File::exists($backupDir)) File::makeDirectory($backupDir, 0755, true);
            $results['tests']['directory'] = [
                'status'     => 'success',
                'path'       => $backupDir,
                'writable'   => is_writable($backupDir),
                'free_space' => $this->formatBytes(disk_free_space($backupDir)),
            ];
        } catch (\Exception $e) {
            $results['tests']['directory'] = ['status' => 'failed', 'error' => $e->getMessage()];
        }

        // Test 3: mysqldump
        exec('mysqldump --version 2>&1', $out, $code);
        $results['tests']['mysqldump'] = [
            'status' => $code === 0 ? 'available' : 'not_available',
            'note'   => $code !== 0 ? 'Will use PHP PDO fallback' : null,
        ];

        // Test 4: PHP extensions
        $results['tests']['extensions'] = [
            'zip'    => extension_loaded('zip')    ? 'ok' : 'missing',
            'pdo'    => extension_loaded('pdo')    ? 'ok' : 'missing',
            'mysqli' => extension_loaded('mysqli') ? 'ok' : 'missing',
        ];

        // Test 5: Queue
        $driver = config('queue.default');
        $results['tests']['queue'] = [
            'driver' => $driver,
            'mode'   => $driver === 'sync' ? 'synchronous' : 'asynchronous',
        ];

        // Overall
        $criticalPassed = ($results['tests']['database']['status'] ?? '') === 'success'
                       && ($results['tests']['directory']['status'] ?? '') === 'success';

        $results['overall_status'] = $criticalPassed ? 'ready' : 'issues_found';
        $results['message']        = $criticalPassed
            ? 'All critical systems ready.'
            : 'Some issues were found. Please review before running a backup.';

        return response()->json($results);
    }

    // ─────────────────────────────────────────────────────────────
    // POST /admin/backups/quick  (JSON — AJAX quick action)
    // ─────────────────────────────────────────────────────────────
    public function quick()
    {
        // Block if one is already running
        if (DataBackup::where('status', 'processing')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'A backup is already in progress. Please wait.',
            ], 409);
        }

        try {
            $backup = $this->backupService->createDatabaseBackup(Auth::id(), 30);

            if ($backup->status === 'completed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Quick backup completed successfully.',
                    'backup'  => [
                        'id'   => $backup->id,
                        'name' => $backup->backup_name,
                        'size' => $backup->formatted_size,
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . ($backup->error_message ?? 'Unknown error'),
            ], 500);

        } catch (\Exception $e) {
            Log::error('DataBackupController@quick failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────
    private function getStats(): array
    {
        $totalSize = DataBackup::where('status', 'completed')->sum('file_size');

        return [
            'total'                => DataBackup::count(),
            'completed'            => DataBackup::where('status', 'completed')->count(),
            'failed'               => DataBackup::where('status', 'failed')->count(),
            'processing'           => DataBackup::where('status', 'processing')->count(),
            'pending'              => DataBackup::where('status', 'pending')->count(),
            'formatted_total_size' => $this->formatBytes($totalSize),
            'latest'               => DataBackup::where('status', 'completed')
                                        ->latest('completed_at')
                                        ->first(),
            'expired_count'        => DataBackup::where('retention_until', '<=', now())->count(),
        ];
    }

    private function formatBytes(int|float $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}