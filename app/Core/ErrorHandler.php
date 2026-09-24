<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class ErrorHandler
{
    public static function register(): void
    {
        $debug = (bool) config('app.debug');
        error_reporting(E_ALL);
        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('log_errors', '1');
        ini_set('error_log', BASE_PATH . '/storage/logs/php-error.log');

        set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        set_exception_handler(static function (Throwable $e): void {
            self::report($e);
            self::render($e);
        });
    }

    public static function report(Throwable $e): void
    {
        $line = sprintf(
            "[%s] %s: %s in %s:%d\n%s\n",
            date('Y-m-d H:i:s'),
            $e::class,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
        @file_put_contents(BASE_PATH . '/storage/logs/app.log', $line, FILE_APPEND | LOCK_EX);
    }

    private static function render(Throwable $e): void
    {
        if (PHP_SAPI === 'cli') {
            fwrite(STDERR, $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL);
            exit(1);
        }
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=UTF-8');
        }
        if (config('app.debug')) {
            echo '<pre style="white-space:pre-wrap;padding:1rem">' . e($e::class . ': ' . $e->getMessage()
                . "\n" . $e->getFile() . ':' . $e->getLine() . "\n\n" . $e->getTraceAsString()) . '</pre>';
            return;
        }
        try {
            echo View::render('errors/500', ['seo' => \App\Services\Seo::simple('Something went wrong', '/', 'noindex,nofollow')]);
        } catch (Throwable) {
            echo '<h1>Something went wrong</h1><p>Please call us on ' . e(business('phone_display')) . '.</p>';
        }
    }
}
