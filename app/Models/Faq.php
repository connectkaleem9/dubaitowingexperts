<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Faq
{
    public const FIELDS = ['question', 'answer', 'category', 'service_id', 'area_id', 'show_on_home', 'sort_order', 'is_published'];

    public static function published(): array
    {
        return Database::all('SELECT * FROM faqs WHERE is_published = 1 ORDER BY category, sort_order, id');
    }

    public static function forHome(int $limit = 6): array
    {
        return Database::all('SELECT * FROM faqs WHERE is_published = 1 AND show_on_home = 1 ORDER BY sort_order, id LIMIT :lim', ['lim' => $limit]);
    }

    public static function forService(int $serviceId): array
    {
        return Database::all('SELECT * FROM faqs WHERE is_published = 1 AND service_id = :s ORDER BY sort_order, id', ['s' => $serviceId]);
    }

    public static function forArea(int $areaId): array
    {
        return Database::all('SELECT * FROM faqs WHERE is_published = 1 AND area_id = :a ORDER BY sort_order, id', ['a' => $areaId]);
    }

    public static function all(): array
    {
        return Database::all(
            'SELECT f.*, s.name AS service_name, a.name AS area_name FROM faqs f
             LEFT JOIN services s ON s.id = f.service_id LEFT JOIN areas a ON a.id = f.area_id
             ORDER BY f.category, f.sort_order, f.id'
        );
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM faqs WHERE id = :id', ['id' => $id]);
    }

    public static function create(array $data): int
    {
        return Database::insert('faqs', array_intersect_key($data, array_flip(self::FIELDS)));
    }

    public static function update(int $id, array $data): void
    {
        Database::update('faqs', $id, array_intersect_key($data, array_flip(self::FIELDS)));
    }

    public static function delete(int $id): void
    {
        Database::delete('faqs', $id);
    }
}
