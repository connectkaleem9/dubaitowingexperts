<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/** Per-path SEO overrides editable in admin (table seo_metadata). */
final class SeoMeta
{
    public static function forPath(string $path): ?array
    {
        try {
            return Database::one('SELECT * FROM seo_metadata WHERE path = :p', ['p' => $path]);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function all(): array
    {
        return Database::all('SELECT * FROM seo_metadata ORDER BY path');
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM seo_metadata WHERE id = :id', ['id' => $id]);
    }

    public static function upsert(string $path, ?string $title, ?string $description, ?string $robots, ?int $ogImageId): void
    {
        Database::run(
            'INSERT INTO seo_metadata (path, title, meta_description, robots, og_image_id) VALUES (:p, :t, :d, :r, :o)
             ON DUPLICATE KEY UPDATE title = VALUES(title), meta_description = VALUES(meta_description),
             robots = VALUES(robots), og_image_id = VALUES(og_image_id)',
            ['p' => $path, 't' => $title, 'd' => $description, 'r' => $robots, 'o' => $ogImageId]
        );
    }

    public static function delete(int $id): void
    {
        Database::delete('seo_metadata', $id);
    }
}
