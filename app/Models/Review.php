<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Review
{
    public const STATUSES = ['pending', 'approved', 'rejected', 'hidden'];

    private const SELECT = 'SELECT r.*, s.name AS service_name, s.slug AS service_slug FROM reviews r LEFT JOIN services s ON s.id = r.service_id';

    /**
     * Every approved review, featured first then newest. The public page deliberately shows them
     * all on one URL with no paging, so no review is ever hidden behind a page number.
     *
     * @return list<array<string, mixed>>
     */
    public static function allApproved(): array
    {
        return Database::all(self::SELECT . " WHERE r.status = 'approved' ORDER BY r.is_featured DESC, r.approved_at DESC");
    }

    /** Featured first, then newest approved; optionally for a service. */
    public static function highlights(int $limit, ?int $serviceId = null): array
    {
        $params = ['lim' => $limit];
        $where = "WHERE r.status = 'approved'";
        if ($serviceId) {
            $where .= ' AND r.service_id = :sid';
            $params['sid'] = $serviceId;
        }
        return Database::all(self::SELECT . " {$where} ORDER BY r.is_featured DESC, r.approved_at DESC LIMIT :lim", $params);
    }

    /** @return array{count: int, average: float} */
    public static function approvedStats(): array
    {
        $row = Database::one("SELECT COUNT(*) AS c, AVG(rating) AS a FROM reviews WHERE status = 'approved'") ?? ['c' => 0, 'a' => 0];
        return ['count' => (int) $row['c'], 'average' => round((float) $row['a'], 1)];
    }

    public static function find(int $id): ?array
    {
        return Database::one(self::SELECT . ' WHERE r.id = :id', ['id' => $id]);
    }

    public static function images(int $reviewId): array
    {
        return Database::all(
            'SELECT m.* FROM review_images ri JOIN media m ON m.id = ri.media_id WHERE ri.review_id = :r',
            ['r' => $reviewId]
        );
    }

    /** @param list<int> $reviewIds @return array<int, list<array<string, mixed>>> */
    public static function imagesFor(array $reviewIds): array
    {
        $reviewIds = array_values(array_filter(array_map('intval', $reviewIds)));
        if ($reviewIds === []) {
            return [];
        }
        $ph = implode(',', array_fill(0, count($reviewIds), '?'));
        $out = [];
        foreach (Database::all("SELECT ri.review_id, m.* FROM review_images ri JOIN media m ON m.id = ri.media_id WHERE ri.review_id IN ({$ph})", $reviewIds) as $row) {
            $out[(int) $row['review_id']][] = $row;
        }
        return $out;
    }

    public static function adminList(int $page, int $perPage, string $status = '', string $search = '', int $rating = 0): array
    {
        $conds = [];
        $params = [];
        if (in_array($status, self::STATUSES, true)) {
            $conds[] = 'r.status = :st';
            $params['st'] = $status;
        }
        if ($rating >= 1 && $rating <= 5) {
            $conds[] = 'r.rating = :rt';
            $params['rt'] = $rating;
        }
        if ($search !== '') {
            $conds[] = '(r.name LIKE :q1 OR r.body LIKE :q2 OR r.email LIKE :q3)';
            $params['q1'] = $params['q2'] = $params['q3'] = '%' . $search . '%';
        }
        $where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';
        $total = (int) Database::value("SELECT COUNT(*) FROM reviews r {$where}", $params);
        $params['lim'] = $perPage;
        $params['off'] = ($page - 1) * $perPage;
        $items = Database::all(self::SELECT . " {$where} ORDER BY r.created_at DESC LIMIT :lim OFFSET :off", $params);
        return ['items' => $items, 'total' => $total];
    }

    public static function countByStatus(): array
    {
        $out = array_fill_keys(self::STATUSES, 0);
        foreach (Database::all('SELECT status, COUNT(*) AS c FROM reviews GROUP BY status') as $r) {
            $out[$r['status']] = (int) $r['c'];
        }
        return $out;
    }

    public static function create(array $data): int
    {
        return Database::insert('reviews', $data);
    }

    public static function attachImage(int $reviewId, int $mediaId): void
    {
        Database::insert('review_images', ['review_id' => $reviewId, 'media_id' => $mediaId]);
    }

    public static function update(int $id, array $data): void
    {
        $allowed = ['name', 'service_id', 'area_text', 'rating', 'body', 'status', 'is_featured', 'admin_note', 'approved_at'];
        Database::update('reviews', $id, array_intersect_key($data, array_flip($allowed)));
    }

    public static function delete(int $id): void
    {
        Database::delete('reviews', $id);
    }
}
