<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Post
{
    public const FIELDS = ['slug', 'title', 'excerpt', 'body', 'featured_image_id', 'status', 'published_at', 'author_id'];

    public static function paginatePublished(int $page, int $perPage): array
    {
        $where = "WHERE status = 'published' AND published_at <= NOW()";
        $total = (int) Database::value("SELECT COUNT(*) FROM posts {$where}");
        $items = Database::all("SELECT * FROM posts {$where} ORDER BY published_at DESC LIMIT :lim OFFSET :off", ['lim' => $perPage, 'off' => ($page - 1) * $perPage]);
        return ['items' => $items, 'total' => $total];
    }

    public static function latest(int $limit, int $excludeId = 0): array
    {
        return Database::all(
            "SELECT * FROM posts WHERE status = 'published' AND published_at <= NOW() AND id <> :ex ORDER BY published_at DESC LIMIT :lim",
            ['ex' => $excludeId, 'lim' => $limit]
        );
    }

    public static function findPublishedBySlug(string $slug): ?array
    {
        return Database::one(
            "SELECT p.*, a.name AS author_name FROM posts p LEFT JOIN admins a ON a.id = p.author_id
             WHERE p.slug = :slug AND p.status = 'published' AND p.published_at <= NOW()",
            ['slug' => $slug]
        );
    }

    public static function allPublished(): array
    {
        return Database::all("SELECT id, slug, updated_at FROM posts WHERE status = 'published' AND published_at <= NOW() ORDER BY published_at DESC");
    }

    public static function adminList(): array
    {
        return Database::all('SELECT * FROM posts ORDER BY updated_at DESC');
    }

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM posts WHERE id = :id', ['id' => $id]);
    }

    public static function slugExists(string $slug, int $exceptId = 0): bool
    {
        return (bool) Database::value('SELECT 1 FROM posts WHERE slug = :s AND id <> :id', ['s' => $slug, 'id' => $exceptId]);
    }

    public static function create(array $data): int
    {
        return Database::insert('posts', array_intersect_key($data, array_flip(self::FIELDS)));
    }

    public static function update(int $id, array $data): void
    {
        Database::update('posts', $id, array_intersect_key($data, array_flip(self::FIELDS)));
    }

    public static function delete(int $id): void
    {
        Database::delete('posts', $id);
    }
}
