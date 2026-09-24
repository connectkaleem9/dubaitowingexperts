<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Project
{
    public const FIELDS = [
        'slug', 'title', 'service_id', 'area_id', 'location_text', 'vehicle_type', 'project_date',
        'excerpt', 'body', 'featured_image_id', 'before_image_id', 'after_image_id', 'status', 'published_at', 'created_by',
    ];

    private const SELECT = 'SELECT p.*, s.name AS service_name, s.slug AS service_slug, a.name AS area_name, a.slug AS area_slug,
        a.is_published AS area_published, s.is_published AS service_published
        FROM projects p
        LEFT JOIN services s ON s.id = p.service_id
        LEFT JOIN areas a ON a.id = p.area_id';

    /** @return array{items: list<array<string, mixed>>, total: int} */
    public static function paginatePublished(int $page, int $perPage, ?int $serviceId = null): array
    {
        $where = "WHERE p.status = 'published'";
        $params = [];
        if ($serviceId) {
            $where .= ' AND p.service_id = :sid';
            $params['sid'] = $serviceId;
        }
        $total = (int) Database::value("SELECT COUNT(*) FROM projects p {$where}", $params);
        $params['lim'] = $perPage;
        $params['off'] = ($page - 1) * $perPage;
        $items = Database::all(self::SELECT . " {$where} ORDER BY COALESCE(p.project_date, DATE(p.published_at)) DESC, p.id DESC LIMIT :lim OFFSET :off", $params);
        return ['items' => $items, 'total' => $total];
    }

    public static function latestPublished(int $limit, ?int $serviceId = null, ?int $areaId = null, int $excludeId = 0): array
    {
        $where = "WHERE p.status = 'published' AND p.id <> :ex";
        $params = ['ex' => $excludeId, 'lim' => $limit];
        if ($serviceId) {
            $where .= ' AND p.service_id = :sid';
            $params['sid'] = $serviceId;
        }
        if ($areaId) {
            $where .= ' AND p.area_id = :aid';
            $params['aid'] = $areaId;
        }
        return Database::all(self::SELECT . " {$where} ORDER BY COALESCE(p.project_date, DATE(p.published_at)) DESC, p.id DESC LIMIT :lim", $params);
    }

    public static function findPublishedBySlug(string $slug): ?array
    {
        return Database::one(self::SELECT . " WHERE p.slug = :slug AND p.status = 'published'", ['slug' => $slug]);
    }

    public static function find(int $id): ?array
    {
        return Database::one(self::SELECT . ' WHERE p.id = :id', ['id' => $id]);
    }

    /** Admin listing with filters. @return array{items: list<array<string, mixed>>, total: int} */
    public static function adminList(int $page, int $perPage, string $status = '', string $search = ''): array
    {
        $conds = [];
        $params = [];
        if (in_array($status, ['draft', 'published'], true)) {
            $conds[] = 'p.status = :st';
            $params['st'] = $status;
        }
        if ($search !== '') {
            $conds[] = '(p.title LIKE :q1 OR p.location_text LIKE :q2)';
            $params['q1'] = '%' . $search . '%';
            $params['q2'] = '%' . $search . '%';
        }
        $where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';
        $total = (int) Database::value("SELECT COUNT(*) FROM projects p {$where}", $params);
        $params['lim'] = $perPage;
        $params['off'] = ($page - 1) * $perPage;
        $items = Database::all(self::SELECT . " {$where} ORDER BY p.updated_at DESC LIMIT :lim OFFSET :off", $params);
        return ['items' => $items, 'total' => $total];
    }

    public static function gallery(int $projectId): array
    {
        return Database::all(
            'SELECT pi.id AS link_id, pi.caption, pi.sort_order, m.* FROM project_images pi
             JOIN media m ON m.id = pi.media_id WHERE pi.project_id = :p ORDER BY pi.sort_order, pi.id',
            ['p' => $projectId]
        );
    }

    public static function slugExists(string $slug, int $exceptId = 0): bool
    {
        return (bool) Database::value('SELECT 1 FROM projects WHERE slug = :s AND id <> :id', ['s' => $slug, 'id' => $exceptId]);
    }

    public static function countByStatus(): array
    {
        $out = ['draft' => 0, 'published' => 0];
        foreach (Database::all('SELECT status, COUNT(*) AS c FROM projects GROUP BY status') as $r) {
            $out[$r['status']] = (int) $r['c'];
        }
        return $out;
    }

    public static function create(array $data): int
    {
        return Database::insert('projects', array_intersect_key($data, array_flip(self::FIELDS)));
    }

    public static function update(int $id, array $data): void
    {
        Database::update('projects', $id, array_intersect_key($data, array_flip(self::FIELDS)));
    }

    public static function delete(int $id): void
    {
        Database::delete('projects', $id);
    }
}
