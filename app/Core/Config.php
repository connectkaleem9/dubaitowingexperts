<?php

declare(strict_types=1);

namespace App\Core;

/** Dot-notation access to config/*.php arrays: Config::get('app.url'). */
final class Config
{
    /** @var array<string, array<string, mixed>> */
    private static array $loaded = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $file = array_shift($parts);
        if (!isset(self::$loaded[$file])) {
            $path = BASE_PATH . '/config/' . basename($file) . '.php';
            self::$loaded[$file] = is_file($path) ? (array) require $path : [];
        }
        $value = self::$loaded[$file];
        foreach ($parts as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }
}
