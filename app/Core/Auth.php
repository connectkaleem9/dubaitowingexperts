<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\RateLimit;

final class Auth
{
    private const MAX_FAILED = 5;          // per account before lock
    private const LOCK_MINUTES = 15;
    private const IP_MAX_ATTEMPTS = 20;    // per IP per 15 minutes

    private static ?array $user = null;

    /** @return string|null Error message, or null on success. */
    public static function attempt(string $email, string $password, string $ip): ?string
    {
        $generic = 'Email or password is incorrect.';
        if (!RateLimit::hit('login-ip:' . $ip, self::IP_MAX_ATTEMPTS, self::LOCK_MINUTES * 60)) {
            return 'Too many login attempts. Please wait 15 minutes and try again.';
        }

        $admin = Admin::findByEmail($email);
        if ($admin === null) {
            Admin::hash($password); // equalise timing with the known-account path
            ActivityLog::record(null, 'login_failed', 'admin', null, 'Unknown email', $ip);
            return $generic;
        }

        if ($admin['locked_until'] !== null && strtotime((string) $admin['locked_until']) > time()) {
            return 'This account is temporarily locked after repeated failed logins. Try again later.';
        }

        if (!(bool) $admin['is_active'] || !password_verify($password, (string) $admin['password_hash'])) {
            $failed = (int) $admin['failed_logins'] + 1;
            Admin::update((int) $admin['id'], [
                'failed_logins' => $failed,
                'locked_until' => $failed >= self::MAX_FAILED ? date('Y-m-d H:i:s', time() + self::LOCK_MINUTES * 60) : null,
            ]);
            ActivityLog::record((int) $admin['id'], 'login_failed', 'admin', (int) $admin['id'], null, $ip);
            return $generic;
        }

        if (password_needs_rehash((string) $admin['password_hash'], defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT)) {
            Admin::setPassword((int) $admin['id'], $password);
        }

        Session::regenerate();
        Csrf::rotate();
        Session::put('admin_id', (int) $admin['id']);
        Session::put('admin_fingerprint', self::fingerprint());
        Admin::update((int) $admin['id'], [
            'failed_logins' => 0,
            'locked_until' => null,
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip,
        ]);
        ActivityLog::record((int) $admin['id'], 'login', 'admin', (int) $admin['id'], null, $ip);
        return null;
    }

    public static function user(): ?array
    {
        if (self::$user !== null) {
            return self::$user;
        }
        $id = Session::get('admin_id');
        if (!is_int($id) || Session::get('admin_fingerprint') !== self::fingerprint()) {
            return null;
        }
        $admin = Admin::find($id);
        if ($admin === null || !(bool) $admin['is_active']) {
            return null;
        }
        return self::$user = $admin;
    }

    public static function id(): ?int
    {
        $u = self::user();
        return $u === null ? null : (int) $u['id'];
    }

    public static function isOwner(): bool
    {
        return (self::user()['role'] ?? '') === 'owner';
    }

    public static function logout(): void
    {
        self::$user = null;
        Session::destroy();
    }

    /** Binds the session to the browser's user agent to make stolen cookies less useful. */
    private static function fingerprint(): string
    {
        return hash('sha256', (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    }
}
