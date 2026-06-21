<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /** Max attempts per message before giving up. */
    private const MAX_ATTEMPTS = 3;

    /** Milliseconds to wait between retry attempts (grows each retry). */
    private const RETRY_DELAY_MS = 800;

    public static function send(string $number, string $message): bool
    {
        $number = self::normalizeNumber($number);

        if (!$number) {
            Log::warning('SmsService: invalid or missing phone number, skipping.', [
                'raw_number' => $number,
            ]);
            return false;
        }

        $driver = config('sms.driver', 'smsgate');

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            try {
                $sent = match ($driver) {
                    'smsgate' => self::sendViaSmsGate($number, $message),
                    default   => throw new \Exception("Unknown SMS driver: {$driver}"),
                };

                if ($sent) {
                    return true;
                }
            } catch (\Throwable $e) {
                Log::error("SmsService [{$driver}] attempt {$attempt}/" . self::MAX_ATTEMPTS . " failed for {$number}: " . $e->getMessage());
            }

            // Don't sleep after the last attempt
            if ($attempt < self::MAX_ATTEMPTS) {
                usleep(self::RETRY_DELAY_MS * 1000 * $attempt); // 800ms, then 1600ms
            }
        }

        Log::error("SmsService: giving up on {$number} after " . self::MAX_ATTEMPTS . " attempts.");
        return false;
    }

    private static function sendViaSmsGate(string $number, string $message): bool
    {
        $e164 = '+63' . substr($number, 1); // 09xxxxxxxxx → +639xxxxxxxxx

        $response = Http::timeout(15)
            ->withBasicAuth(
                config('sms.smsgate.username'),
                config('sms.smsgate.password')
            )->post(config('sms.smsgate.endpoint'), [
                'textMessage'  => ['text' => $message],
                'phoneNumbers' => [$e164],
            ]);

        if ($response->successful()) {
            Log::info("SMS sent via SMSGate to {$number}");
            return true;
        }

        Log::warning("SMSGate failed for {$number}: [{$response->status()}] " . $response->body());
        return false;
    }

    private static function normalizeNumber(?string $number): ?string
    {
        if (!$number) return null;
        $digits = preg_replace('/\D/', '', $number);

        // Convert +639xxxxxxxxx → 09xxxxxxxxx
        if (str_starts_with($digits, '63') && strlen($digits) === 12) {
            $digits = '0' . substr($digits, 2);
        }

        return (strlen($digits) === 11 && str_starts_with($digits, '09'))
            ? $digits
            : null;
    }
}