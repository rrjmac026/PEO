<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public static function send(string $number, string $message): bool
    {
        $number = self::normalizeNumber($number);

        if (!$number) {
            Log::warning('SmsService: invalid or missing phone number, skipping.');
            return false;
        }

        $driver = config('sms.driver', 'smsgate');

        try {
            return match ($driver) {
                'smsgate' => self::sendViaSmsGate($number, $message),
                default   => throw new \Exception("Unknown SMS driver: {$driver}"),
            };
        } catch (\Throwable $e) {
            Log::error("SmsService [{$driver}] failed for {$number}: " . $e->getMessage());
            return false;
        }
    }

    private static function sendViaSmsGate(string $number, string $message): bool
    {
        $e164 = '+63' . substr($number, 1); // 09xxxxxxxxx → +639xxxxxxxxx

        $response = Http::withBasicAuth(
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

        Log::warning("SMSGate failed for {$number}: " . $response->body());
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