<?php

declare(strict_types=1);

/*
 * Router for PHP's built-in server ONLY (local development):
 *   php -S 127.0.0.1:8080 -t public public/router-dev.php
 * Production uses public/.htaccess. This file is never used by Apache.
 */

$path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file) && !str_ends_with($path, '.php')) {
    return false; // let the built-in server serve the static file
}

require __DIR__ . '/index.php';
