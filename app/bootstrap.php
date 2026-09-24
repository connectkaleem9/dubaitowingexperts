<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'App\\'   => BASE_PATH . '/app/',
        'Admin\\' => BASE_PATH . '/admin/',
        'Tests\\' => BASE_PATH . '/tests/',
    ];
    foreach ($prefixes as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_file($file)) {
                require $file;
            }
            return;
        }
    }
});

require BASE_PATH . '/app/Helpers/functions.php';

App\Core\Env::load(BASE_PATH . '/.env');
date_default_timezone_set((string) config('app.timezone'));
mb_internal_encoding('UTF-8');
App\Core\ErrorHandler::register();
