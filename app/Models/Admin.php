<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Admin
{
    public const ROLES = ['owner' => 'Owner (full access)', 'editor' => 'Editor (content only)'];

    public static function find(int $id): ?array
    {
        return Database::one('SELECT * FROM admins WHERE id = :id', ['id' => $id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::one('SELECT * FROM admins WHERE email = :e', ['e' => mb_strtolower($email)]);
    }

    public static function all(): array
    {
        return Database::all('SELECT id, name, email, role, is_active, last_login_at, created_at FROM admins ORDER BY name');
    }

    public static function countActiveOwners(): int
    {
        return (int) Database::value("SELECT COUNT(*) FROM admins WHERE role = 'owner' AND is_active = 1");
    }

    public static function create(string $name, string $email, string $password, string $role): int
    {
        return Database::insert('admins', [
            'name' => $name,
            'email' => mb_strtolower($email),
            'password_hash' => self::hash($password),
            'role' => $role,
        ]);
    }

    public static function update(int $id, array $data): void
    {
        if (isset($data['email'])) {
            $data['email'] = mb_strtolower((string) $data['email']);
        }
        Database::update('admins', $id, array_intersect_key($data, array_flip(['name', 'email', 'role', 'is_active', 'failed_logins', 'locked_until', 'last_login_at', 'last_login_ip', 'password_hash'])));
    }

    public static function setPassword(int $id, string $password): void
    {
        Database::update('admins', $id, ['password_hash' => self::hash($password)]);
    }

    public static function hash(string $password): string
    {
        return defined('PASSWORD_ARGON2ID')
            ? password_hash($password, PASSWORD_ARGON2ID)
            : password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function delete(int $id): void
    {
        Database::delete('admins', $id);
    }
}
