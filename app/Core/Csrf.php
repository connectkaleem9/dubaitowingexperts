<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        $token = Session::get('_csrf');
        if (!is_string($token) || strlen($token) !== 64) {
            $token = bin2hex(random_bytes(32));
            Session::put('_csrf', $token);
        }
        return $token;
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(self::token()) . '">';
    }

    public static function valid(string $submitted): bool
    {
        $token = Session::get('_csrf');
        return is_string($token) && $submitted !== '' && hash_equals($token, $submitted);
    }

    public static function rotate(): void
    {
        Session::forget('_csrf');
    }
}
