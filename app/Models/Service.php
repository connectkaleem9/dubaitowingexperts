<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Service
{
    public const FIELDS = ['slug', 'name', 'h1', 'excerpt', 'intro', 'body', 'icon', 'whatsapp_message', 'image_id', 'sort_order', 'is_published'];

    /** @return list<array<string, mixed>> */
    public static function published(): array
    {
        return Database::all('SELECT * FROM services WHERE is_published = 1 ORDER BY sort_order, name');
    }

    /** @return list<array<string, mixed>> */
    public static function all(): array
    {
        return Database::all('SELECT * FROM services ORDER BY sort_order, name');
    }

    public static function findPublishedBySlug(string $slug): ?array
    {
        return Database::one('SELECT * FROM services WHERE slug = :slug AND is_published = 1', ['slug' => $slug]);
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM services WHERE id = :id', ['id' => $id]);
    }

    public static function slugExists(string $slug, int $exceptId = 0): bool
    {
        return (bool) Database::value('SELECT 1 FROM services WHERE slug = :s AND id <> :id', ['s' => $slug, 'id' => $exceptId]);
    }

    /** Published services linked to an area. */
    public static function forArea(int $areaId): array
    {
        return Database::all(
            'SELECT s.* FROM services s JOIN service_area sa ON sa.service_id = s.id
             WHERE sa.area_id = :a AND s.is_published = 1 ORDER BY s.sort_order, s.name',
            ['a' => $areaId]
        );
    }

    /** @return list<int> */
    public static function areaIds(int $serviceId): array
    {
        return array_map('intval', array_column(
            Database::all('SELECT area_id FROM service_area WHERE service_id = :s', ['s' => $serviceId]),
            'area_id'
        ));
    }

    /** @param list<int> $areaIds */
    public static function syncAreas(int $serviceId, array $areaIds): void
    {
        Database::transaction(static function () use ($serviceId, $areaIds): void {
            Database::run('DELETE FROM service_area WHERE service_id = :s', ['s' => $serviceId]);
            foreach (array_unique($areaIds) as $areaId) {
                Database::run('INSERT IGNORE INTO service_area (service_id, area_id) VALUES (:s, :a)', ['s' => $serviceId, 'a' => $areaId]);
            }
        });
    }

    public static function create(array $data): int
    {
        return Database::insert('services', array_intersect_key($data, array_flip(self::FIELDS)));
    }

    public static function update(int $id, array $data): void
    {
        Database::update('services', $id, array_intersect_key($data, array_flip(self::FIELDS)));
    }

    public static function delete(int $id): void
    {
        Database::delete('services', $id);
    }
}
