<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Area
{
    public const FIELDS = ['slug', 'name', 'h1', 'excerpt', 'intro', 'body', 'whatsapp_message', 'image_id', 'sort_order', 'is_published'];

    public static function published(): array
    {
        return Database::all('SELECT * FROM areas WHERE is_published = 1 ORDER BY sort_order, name');
    }

    public static function all(): array
    {
        return Database::all('SELECT * FROM areas ORDER BY sort_order, name');
    }

    public static function findPublishedBySlug(string $slug): ?array
    {
        return Database::one('SELECT * FROM areas WHERE slug = :slug AND is_published = 1', ['slug' => $slug]);
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM areas WHERE id = :id', ['id' => $id]);
    }

    public static function slugExists(string $slug, int $exceptId = 0): bool
    {
        return (bool) Database::value('SELECT 1 FROM areas WHERE slug = :s AND id <> :id', ['s' => $slug, 'id' => $exceptId]);
    }

    /** Published areas linked to a service. */
    public static function forService(int $serviceId): array
    {
        return Database::all(
            'SELECT a.* FROM areas a JOIN service_area sa ON sa.area_id = a.id
             WHERE sa.service_id = :s AND a.is_published = 1 ORDER BY a.sort_order, a.name',
            ['s' => $serviceId]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert('areas', array_intersect_key($data, array_flip(self::FIELDS)));
    }

    public static function update(int $id, array $data): void
    {
        Database::update('areas', $id, array_intersect_key($data, array_flip(self::FIELDS)));
    }

    public static function delete(int $id): void
    {
        Database::delete('areas', $id);
    }
}
