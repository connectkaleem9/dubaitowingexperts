<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/** Fixed-window counter stored in MySQL (works on shared hosting without Redis/APCu). */
final class RateLimit
{
    /** Records a hit and returns true if the bucket is still within its limit. */
    public static function hit(string $bucket, int $max, int $windowSeconds): bool
    {
        $bucket = substr(hash('sha256', $bucket), 0, 64);
        Database::run(
            'INSERT INTO rate_limits (bucket, hits, reset_at) VALUES (:b, 1, DATE_ADD(NOW(), INTERVAL :w SECOND))
             ON DUPLICATE KEY UPDATE
               hits = IF(reset_at < NOW(), 1, hits + 1),
               reset_at = IF(reset_at < NOW(), DATE_ADD(NOW(), INTERVAL :w2 SECOND), reset_at)',
            ['b' => $bucket, 'w' => $windowSeconds, 'w2' => $windowSeconds]
        );
        $hits = (int) Database::value('SELECT hits FROM rate_limits WHERE bucket = :b', ['b' => $bucket]);
        if (random_int(1, 50) === 1) {
            Database::run('DELETE FROM rate_limits WHERE reset_at < NOW() - INTERVAL 1 DAY');
        }
        return $hits <= $max;
    }

    public static function clear(string $bucket): void
    {
        Database::run('DELETE FROM rate_limits WHERE bucket = :b', ['b' => substr(hash('sha256', $bucket), 0, 64)]);
    }
}
