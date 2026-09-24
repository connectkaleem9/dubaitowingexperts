<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;

final class SecurityHeaders
{
    public function handle(Request $request): void
    {
        $nonce = csp_nonce();
        // Strict nonce-based CSP (Google's recommended form) — allows GTM/gtag loaded by a nonced script.
        $csp = implode('; ', [
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
            "frame-ancestors 'self'",
        ]);
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
