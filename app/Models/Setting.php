<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Setting
{
    /** @var array<string, string|null>|null */
    private static ?array $cache = null;

    public static function get(string $key): ?string
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (Database::all('SELECT `key`, `value` FROM settings') as $row) {
                self::$cache[$row['key']] = $row['value'];
            }
        }
        return self::$cache[$key] ?? null;
    }

    /** @return array<string, string|null> */
    public static function all(): array
    {
        self::get('__warm__');
        return self::$cache ?? [];
    }

    public static function set(string $key, ?string $value): void
    {
        Database::run(
            'INSERT INTO settings (`key`, `value`) VALUES (:k, :v) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
            ['k' => $key, 'v' => $value]
        );
        if (self::$cache !== null) {
            self::$cache[$key] = $value;
        }
    }
}
