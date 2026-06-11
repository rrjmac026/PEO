<?php

namespace App\Jobs;

use App\Services\BackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateDatabaseBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // 5 minutes

    public function __construct(
        public readonly int    $userId,
        public readonly string $backupType    = 'database',
        public readonly int    $retentionDays = 30,
    ) {}

    public function handle(BackupService $backupService): void
    {
        if ($this->backupType === 'full') {
            $backupService->createFullBackup($this->userId, $this->retentionDays);
        } else {
            $backupService->createDatabaseBackup($this->userId, $this->retentionDays);
        }
    }
}