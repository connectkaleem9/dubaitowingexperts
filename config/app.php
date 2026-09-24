<?php

declare(strict_types=1);

return [
    'env'           => env('APP_ENV', 'production'),
    'debug'         => env('APP_DEBUG', false) === true,
    'url'           => rtrim((string) env('APP_URL', 'https://dubaitowingexperts.com'), '/'),
    'key'           => (string) env('APP_KEY', ''),
    'timezone'      => 'Asia/Dubai',
    'force_noindex' => env('FORCE_NOINDEX', false) === true,
    'mail_from'     => (string) env('MAIL_FROM', 'no-reply@dubaitowingexperts.com'),
    'mail_to'       => (string) env('MAIL_TO', ''),
    'session_name'  => 'dre_session',
    'upload_max_bytes' => 8 * 1024 * 1024,
    'image_widths'  => [400, 800, 1600],
    'per_page'      => 12,
];
