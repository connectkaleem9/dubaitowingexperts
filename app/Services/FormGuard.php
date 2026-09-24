<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Request;
use App\Models\RateLimit;

/**
 * Anti-spam for public forms: honeypot field, signed render timestamp (min fill time),
 * and per-IP rate limit. CSRF is enforced globally.
 */
final class FormGuard
{
    public const SPAM = 'spam';

    public static function fields(): string
    {
        $ts = (string) time();
        $sig = hash_hmac('sha256', $ts, (string) config('app.key', 'dre'));
        return '<div class="hp" aria-hidden="true"><label for="hp-website">Leave this field empty</label>'
            . '<input type="text" id="hp-website" name="website" tabindex="-1" autocomplete="off"></div>'
            . '<input type="hidden" name="_ts" value="' . e($ts . '.' . $sig) . '">';
    }

    /** @return string|null null = OK, self::SPAM = silently drop, other string = user-facing error */
    public static function check(Request $request, string $bucket, int $max, int $windowSeconds): ?string
    {
        if ($request->input('website') !== '') {
            return self::SPAM;
        }
        [$ts, $sig] = array_pad(explode('.', $request->input('_ts'), 2), 2, '');
        $expected = hash_hmac('sha256', $ts, (string) config('app.key', 'dre'));
        if ($ts === '' || !hash_equals($expected, $sig)) {
            return 'Please reload the page and try again.';
        }
        $age = time() - (int) $ts;
        if ($age < 3) {
            return self::SPAM;
        }
        if ($age > 86400) {
            return 'This form expired. Please reload the page and try again.';
        }
        if (!RateLimit::hit($bucket . ':' . $request->ip(), $max, $windowSeconds)) {
            return 'Too many submissions from your connection. Please call or WhatsApp us instead.';
        }
        return null;
    }
}
