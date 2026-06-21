<?php

return [
    'driver' => env('SMS_DRIVER', 'smsgate'),

    'smsgate' => [
        'endpoint' => env('SMSGATE_ENDPOINT', 'https://api.sms-gate.app/3rdparty/v1/messages'),
        'username' => env('SMSGATE_USERNAME'),
        'password' => env('SMSGATE_PASSWORD'),
    ],
];