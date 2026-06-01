<?php
// Save as: app/Console/Commands/TestSemaphore.php
// Run with: php artisan sms:test --number=09171234567

namespace App\Console\Commands;

use App\Services\SemaphoreService;
use Illuminate\Console\Command;

class TestSemaphore extends Command
{
    protected $signature   = 'sms:test {--number= : Philippine mobile number to send to}';
    protected $description = 'Test Semaphore SMS integration';

    public function handle(SemaphoreService $sms): int
    {
        // ── 1. Check credits ─────────────────────────────────────────────────
        $this->info('Checking Semaphore account credits…');
        $credits = $sms->getCredits();

        if ($credits === null) {
            $this->error('Could not reach Semaphore API. Check your SEMAPHORE_API_KEY in .env');
            return self::FAILURE;
        }

        $this->line("  Credits remaining: <comment>{$credits}</comment>");

        if ($credits < 1) {
            $this->warn('No credits left — top up at semaphore.ph before sending.');
            return self::FAILURE;
        }

        // ── 2. Send a test SMS ────────────────────────────────────────────────
        $number = $this->option('number');

        if (!$number) {
            $number = $this->ask('Enter a Philippine mobile number to test (e.g. 09171234567)');
        }

        $this->info("Sending test SMS to {$number}…");

        $sent = $sms->send(
            $number,
            '[PEO System] Test SMS from Bukidnon Provincial Engineer\'s Office system. Integration is working!'
        );

        if ($sent) {
            $this->info('SMS sent successfully! Check the phone.');
        } else {
            $this->error('SMS failed. Check storage/logs/laravel.log for details.');
            return self::FAILURE;
        }

        // ── 3. Simulate a Work Request notification ───────────────────────────
        if ($this->confirm('Also test a Work Request SMS notification format?', true)) {
            $sms->send(
                $number,
                '[Work Request] New submission from Juan dela Cruz: "Road Repair, Maramag". Please log in to assign reviewers.'
            );
            $this->line('  Work Request SMS sent.');
        }

        // ── 4. Simulate a Concrete Pouring notification ───────────────────────
        if ($this->confirm('Also test a Concrete Pouring SMS notification format?', true)) {
            $sms->send(
                $number,
                '[Concrete Pouring] Request CN-2025-0001 for "Bridge Slab" is awaiting your review. Log in to proceed.'
            );
            $this->line('  Concrete Pouring SMS sent.');
        }

        $this->newLine();
        $this->info('All tests complete.');

        return self::SUCCESS;
    }
}