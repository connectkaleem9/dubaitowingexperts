<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/** Contact / quote submissions (table contact_submissions). */
final class Lead
{
    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'in_progress' => 'In progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    private const SELECT = 'SELECT l.*, s.name AS service_name FROM contact_submissions l LEFT JOIN services s ON s.id = l.service_id';

    public static function create(array $data): int
    {
        return Database::insert('contact_submissions', $data);
    }

    public static function find(int $id): ?array
    {
        return Database::one(self::SELECT . ' WHERE l.id = :id', ['id' => $id]);
    }

    public static function adminList(int $page, int $perPage, string $status = '', string $search = ''): array
    {
        $conds = [];
        $params = [];
        if (array_key_exists($status, self::STATUSES)) {
            $conds[] = 'l.status = :st';
            $params['st'] = $status;
        }
        if ($search !== '') {
            $conds[] = '(l.name LIKE :q1 OR l.phone LIKE :q2 OR l.location LIKE :q3 OR l.message LIKE :q4)';
            $params['q1'] = $params['q2'] = $params['q3'] = $params['q4'] = '%' . $search . '%';
        }
        $where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';
        $total = (int) Database::value("SELECT COUNT(*) FROM contact_submissions l {$where}", $params);
        $params['lim'] = $perPage;
        $params['off'] = ($page - 1) * $perPage;
        $items = Database::all(self::SELECT . " {$where} ORDER BY l.created_at DESC LIMIT :lim OFFSET :off", $params);
        return ['items' => $items, 'total' => $total];
    }

    public static function recent(int $limit): array
    {
        return Database::all(self::SELECT . ' ORDER BY l.created_at DESC LIMIT :lim', ['lim' => $limit]);
    }

    public static function countByStatus(): array
    {
        $out = array_fill_keys(array_keys(self::STATUSES), 0);
        foreach (Database::all('SELECT status, COUNT(*) AS c FROM contact_submissions GROUP BY status') as $r) {
            $out[$r['status']] = (int) $r['c'];
        }
        return $out;
    }

    public static function countSince(string $datetime): int
    {
        return (int) Database::value('SELECT COUNT(*) FROM contact_submissions WHERE created_at >= :d', ['d' => $datetime]);
    }

    public static function update(int $id, array $data): void
    {
        Database::update('contact_submissions', $id, array_intersect_key($data, array_flip(['status', 'admin_notes'])));
    }

    public static function delete(int $id): void
    {
        Database::delete('contact_submissions', $id);
    }
}
