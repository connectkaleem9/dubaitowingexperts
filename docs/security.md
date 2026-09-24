# Security

Owner: Security agent · Updated 2026-09-22 · Re-run this checklist after any change to auth,
forms, uploads, sessions or admin routes.

## Controls in place

| Area | Control | Where |
|---|---|---|
| SQL injection | PDO prepared statements only; identifiers validated against `^[a-z_][a-z0-9_]{0,63}$`; no string interpolation | `app/Core/Database.php`, `app/Models/*` |
| XSS (reflected) | `e()` (htmlspecialchars, ENT_QUOTES) on every dynamic output | all views |
| XSS (stored) | Admin rich text passes through an allow-list DOM sanitiser (no script/style/iframe/form/event handlers, safe href schemes only) | `app/Services/HtmlSanitizer.php` |
| XSS (defence in depth) | CSP with per-request nonce + `strict-dynamic`, `object-src 'none'`, `base-uri 'self'`, `frame-ancestors 'self'` | `app/Middleware/SecurityHeaders.php` |
| CSRF | 64-char token in the session, checked on **every** POST; 419 on mismatch; rotated on login | `app/Middleware/VerifyCsrf.php`, `app/Core/Csrf.php` |
| Passwords | Argon2id (bcrypt cost 12 fallback), rehash on login when parameters change, min 12 characters | `app/Models/Admin.php` |
| Brute force | 5 failed attempts → 15-minute account lock; 20 attempts per IP per 15 minutes; generic error message; timing equalised for unknown emails | `app/Core/Auth.php` |
| Sessions | `HttpOnly`, `SameSite=Lax`, `Secure` on HTTPS, strict mode, 48-char IDs, regenerated on login, 2-hour idle expiry, user-agent fingerprint | `app/Core/Session.php`, `app/Core/Auth.php` |
| Authorization | `Authenticate` on all `/admin/*`; `OwnerOnly` on settings, users, activity, destructive deletes; role re-checked from the database on every request | `app/Middleware/*`, `routes/admin.php` |
| Form abuse | Honeypot field, HMAC-signed render timestamp (min 3 s), per-IP rate limits (leads 5/10 min, reviews 3/h) | `app/Services/FormGuard.php` |
| File uploads | Extension-independent `finfo` MIME allow-list (JPEG/PNG/WebP), `getimagesize` + pixel cap, 8 MB limit, full re-encode through GD to WebP, random filenames, `public/uploads/.htaccess` disables execution and forces `X-Content-Type-Options` + sandbox CSP | `app/Services/Uploader.php` |
| Open redirects | Redirect targets restricted to internal paths (`safeReturnPath`, `redirect_back`, admin `back` params) | controllers |
| IDOR | Every admin action loads the record by validated integer id and 404s if missing; gallery deletes are scoped to the parent project | `admin/Controllers/*` |
| Secrets | Credentials only in `.env` (git-ignored, outside `public/`); root `.htaccess` denies dotfiles and source directories | `.env.example`, `.htaccess` |
| Transport | HTTPS + non-www redirect, HSTS (1 year, includeSubDomains) when served over TLS | `public/.htaccess`, `SecurityHeaders` |
| Privacy | IPs stored as HMAC only; EXIF (including GPS) removed on upload; no personal data in URLs | `functions.php`, `Uploader` |
| Audit | Every admin create/update/delete/moderation/login writes an `activity_logs` row | `admin/Controllers/AdminController::log()` |

## Pre-launch checklist

- [ ] `.env`: `APP_ENV=production`, `APP_DEBUG=false`, strong unique `APP_KEY`, real DB credentials
- [ ] `.env` is outside the web root and not readable over HTTP (test `https://domain/.env` → 403/404)
- [ ] Document root points at `public/`
- [ ] HTTPS certificate valid; `http://` and `www.` both 301 to the canonical host
- [ ] `https://domain/admin/` redirects to the login page when signed out
- [ ] Admin accounts: unique per person, 12+ character passwords, no shared logins
- [ ] Delete any test/demo admin accounts (`/admin/users/`)
- [ ] Upload a non-image file in the admin → rejected; upload a `.php` renamed to `.jpg` → rejected
- [ ] Request an uploaded file with `?x=<script>` → served as an image, not executed
- [ ] Submit a form with a missing/altered CSRF token → 419
- [ ] Submit `<script>alert(1)</script>` in a review → stored escaped, rendered inert
- [ ] Try `/admin/settings/` as an editor → 403
- [ ] `storage/` and `database/` are not reachable over HTTP
- [ ] Server error pages do not show stack traces
- [ ] Backups configured (database + `public/uploads/`)

## Known limitations / follow-ups

- Password reset by email is not implemented — an owner resets another admin's password in
  `/admin/users/`. Add email reset only with a signed, expiring, single-use token.
- Two-factor authentication is not implemented. Worth adding if more people get admin access.
- Rate limiting is per IP; shared office/mobile NAT can group users together. Limits are deliberately
  generous to avoid blocking genuine emergency enquiries.
- `Mailer` uses PHP `mail()`. If the host is unreliable, switch to authenticated SMTP and add SPF/DKIM.
