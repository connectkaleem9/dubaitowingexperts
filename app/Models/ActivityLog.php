<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class ActivityLog
{
    public static function record(?int $adminId, string $action, ?string $entityType = null, ?int $entityId = null, ?string $details = null, ?string $ip = null): void
    {
        Database::insert('activity_logs', [
            'admin_id' => $adminId,
            'action' => mb_substr($action, 0, 60),
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => $details === null ? null : mb_substr($details, 0, 500),
            'ip' => $ip,
        ]);
    }

    public static function paginate(int $page, int $perPage, string $action = ''): array
    {
        $where = '';
        $params = [];
        if ($action !== '') {
            $where = 'WHERE l.action = :a';
            $params['a'] = $action;
        }
        $total = (int) Database::value("SELECT COUNT(*) FROM activity_logs l {$where}", $params);
        $params['lim'] = $perPage;
        $params['off'] = ($page - 1) * $perPage;
        $items = Database::all(
            "SELECT l.*, a.name AS admin_name FROM activity_logs l LEFT JOIN admins a ON a.id = l.admin_id {$where}
             ORDER BY l.id DESC LIMIT :lim OFFSET :off",
            $params
        );
        return ['items' => $items, 'total' => $total];
    }

    public static function actions(): array
    {
        return array_column(Database::all('SELECT DISTINCT action FROM activity_logs ORDER BY action'), 'action');
    }
}
