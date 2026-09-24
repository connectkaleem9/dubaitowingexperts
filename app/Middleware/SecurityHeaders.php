<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;

final class SecurityHeaders
{
    /**
     * Strict nonce-based CSP (Google's recommended form) — allows GTM/gtag loaded by a nonced script.
     *
     * Some hosts (Hostinger's LiteSpeed among them) overwrite the CSP *header* at server level, so
     * the same policy is also emitted as a <meta http-equiv> tag, which no host can replace. Any
     * directive a meta tag cannot carry is listed in $headerOnly and stays in .htaccess, where a
     * server-level header can be overridden back.
     *
     * @return list<string>
     */
    public static function cspDirectives(string $nonce): array
    {
        return [
            "default-src 'self'",
            "script-src 'nonce-{$nonce}' 'strict-dynamic' https: 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: https:",
            "font-src 'self'",
            "connect-src 'self' https:",
            "frame-src https://www.google.com https://www.googletagmanager.com https://td.doubleclick.net",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ];
    }

    /** Directives browsers ignore inside a <meta> tag, so they only ever work as a header. */
    public const HEADER_ONLY = ["frame-ancestors 'self'"];

    public function handle(Request $request): void
    {
        $csp = implode('; ', [...self::cspDirectives(csp_nonce()), ...self::HEADER_ONLY]);
        header('Content-Security-Policy: ' . $csp);
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(self), payment=()');
        header('X-Frame-Options: SAMEORIGIN');
        header('Cross-Origin-Opener-Policy: same-origin-allow-popups');
        if ($request->isSecure()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        if (config('app.force_noindex')) {
            header('X-Robots-Tag: noindex, nofollow');
        }
        header_remove('X-Powered-By');
    }
}
