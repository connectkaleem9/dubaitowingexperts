<?php

declare(strict_types=1);

namespace App\Core;

/** Minimal .env loader: KEY=value, # comments, optional quotes. */
final class Env
{
    /** @var array<string, string|bool|null> */
    private static array $values = [];

    public static function load(string $file): void
    {
        if (!is_file($file)) {
            return;
        }
        $contents = (string) file_get_contents($file);
        // Editors such as Notepad save a UTF-8 BOM, which would corrupt the first key name.
        $contents = (string) preg_replace('/^\xEF\xBB\xBF/', '', $contents);
        foreach (preg_split('/\R/', $contents) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
                $quote = $value[0];
                $end = strpos($value, $quote, 1);
                $value = $end === false ? substr($value, 1) : substr($value, 1, $end - 1);
            } else {
                $value = trim((string) preg_replace('/\s+#.*$/', '', $value));
            }
            self::$values[$key] = self::cast($value);
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$values)) {
            return self::$values[$key];
        }
        $server = getenv($key);
        return $server === false ? $default : self::cast($server);
    }

    private static function cast(string $value): string|bool|null
    {
        return match (strtolower($value)) {
            'true'  => true,
            'false' => false,
            'null'  => null,
            default => $value,
        };
    }
}
