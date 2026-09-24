<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Media
{
    public static function find(?int $id): ?array
    {
        if ($id === null || $id <= 0) {
            return null;
        }
        return Database::one('SELECT * FROM media WHERE id = :id', ['id' => $id]);
    }

    /** @param list<int> $ids @return array<int, array<string, mixed>> keyed by id */
    public static function findMany(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($ids === []) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $out = [];
        foreach (Database::all("SELECT * FROM media WHERE id IN ({$placeholders})", $ids) as $row) {
            $out[(int) $row['id']] = $row;
        }
        return $out;
    }

    /** @return array{items: list<array<string, mixed>>, total: int} */
    public static function paginate(int $page, int $perPage, string $search = ''): array
    {
        $where = '';
        $params = [];
        if ($search !== '') {
            $where = 'WHERE alt_text LIKE :q1 OR original_name LIKE :q2';
            $params = ['q1' => '%' . $search . '%', 'q2' => '%' . $search . '%'];
        }
        $total = (int) Database::value("SELECT COUNT(*) FROM media {$where}", $params);
        $params['lim'] = $perPage;
        $params['off'] = ($page - 1) * $perPage;
        $items = Database::all("SELECT * FROM media {$where} ORDER BY id DESC LIMIT :lim OFFSET :off", $params);
        return ['items' => $items, 'total' => $total];
    }

    public static function create(array $data): int
    {
        return Database::insert('media', $data);
    }

    public static function updateAlt(int $id, string $alt): void
    {
        Database::update('media', $id, ['alt_text' => $alt]);
    }

    /** Where is this media used? Returns human-readable references. */
    public static function usages(int $id): array
    {
        $checks = [
            'project (featured)' => 'SELECT id, title AS label FROM projects WHERE featured_image_id = :id',
            'project (before)'   => 'SELECT id, title AS label FROM projects WHERE before_image_id = :id',
            'project (after)'    => 'SELECT id, title AS label FROM projects WHERE after_image_id = :id',
            'project gallery'    => 'SELECT p.id, p.title AS label FROM project_images pi JOIN projects p ON p.id = pi.project_id WHERE pi.media_id = :id',
            'service'            => 'SELECT id, name AS label FROM services WHERE image_id = :id',
            'area'               => 'SELECT id, name AS label FROM areas WHERE image_id = :id',
            'blog post'          => 'SELECT id, title AS label FROM posts WHERE featured_image_id = :id',
            'review'             => 'SELECT r.id, r.name AS label FROM review_images ri JOIN reviews r ON r.id = ri.review_id WHERE ri.media_id = :id',
            'SEO OG image'       => 'SELECT id, path AS label FROM seo_metadata WHERE og_image_id = :id',
        ];
        $out = [];
        foreach ($checks as $type => $sql) {
            foreach (Database::all($sql, ['id' => $id]) as $row) {
                $out[] = $type . ': ' . $row['label'];
            }
        }
        return $out;
    }

    public static function delete(int $id): void
    {
        Database::delete('media', $id);
    }
}
