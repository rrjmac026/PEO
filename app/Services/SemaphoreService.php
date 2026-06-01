<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SemaphoreService
{
    private string $apiKey;
    private string $senderName;
    private const API_URL = 'https://api.semaphore.co/api/v4/messages';

    public function __construct()
    {
        $this->apiKey     = config('services.semaphore.api_key');
        $this->senderName = config('services.semaphore.sender_name', 'SEMAPHORE');
    }

    /**
     * Send a single SMS message.
     *
     * @param  string  $number  Philippine mobile number (e.g. 09171234567 or +639171234567)
     * @param  string  $message Message body (max 160 chars per SMS credit)
     * @return bool
     */
    public function send(string $number, string $message): bool
    {
        $number = $this->formatNumber($number);

        try {
            $response = Http::post(self::API_URL, [
                'apikey'      => $this->apiKey,
                'number'      => $number,
                'message'     => $message,
                'sendername'  => $this->senderName,
            ]);

            if ($response->successful()) {
                Log::info('Semaphore SMS sent', [
                    'to'     => $number,
                    'status' => $response->json(),
                ]);
                return true;
            }

            Log::error('Semaphore SMS failed', [
                'to'     => $number,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;

        } catch (\Throwable $e) {
            Log::error('Semaphore SMS exception', [
                'to'    => $number,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send SMS to multiple numbers at once.
     * Semaphore accepts comma-separated numbers in a single request.
     *
     * @param  array   $numbers  Array of mobile numbers
     * @param  string  $message
     * @return bool
     */
    public function sendBulk(array $numbers, string $message): bool
    {
        $formatted = implode(',', array_map([$this, 'formatNumber'], $numbers));

        try {
            $response = Http::post(self::API_URL, [
                'apikey'     => $this->apiKey,
                'number'     => $formatted,
                'message'    => $message,
                'sendername' => $this->senderName,
            ]);

            if ($response->successful()) {
                Log::info('Semaphore bulk SMS sent', [
                    'count'  => count($numbers),
                    'status' => $response->json(),
                ]);
                return true;
            }

            Log::error('Semaphore bulk SMS failed', [
                'count'  => count($numbers),
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;

        } catch (\Throwable $e) {
            Log::error('Semaphore bulk SMS exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check remaining SMS credits on your account.
     *
     * @return int|null  Returns credit count, or null on failure
     */
    public function getCredits(): ?int
    {
        try {
            $response = Http::get('https://api.semaphore.co/api/v4/account', [
                'apikey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json('credit_balance');
            }
        } catch (\Throwable $e) {
            Log::error('Semaphore credits check failed', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Normalize a Philippine mobile number to the format Semaphore expects:
     * 09171234567 (11 digits, starts with 09)
     */
    private function formatNumber(string $number): string
    {
        // Remove spaces, dashes, parentheses
        $number = preg_replace('/[\s\-\(\)]/', '', $number);

        // +63XXXXXXXXXX → 0XXXXXXXXXX
        if (str_starts_with($number, '+63')) {
            $number = '0' . substr($number, 3);
        }

        // 63XXXXXXXXXX → 0XXXXXXXXXX
        if (str_starts_with($number, '63') && strlen($number) === 12) {
            $number = '0' . substr($number, 2);
        }

        return $number;
    }
}