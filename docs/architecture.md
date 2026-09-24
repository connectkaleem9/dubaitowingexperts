# Architecture

Updated 2026-09-22 · Decisions: `docs/decisions.md`

## Request lifecycle

```
Apache (public/.htaccess)            → HTTPS + non-www + front controller
public/index.php
  ├── app/bootstrap.php              autoloader, helpers, .env, error handler
  ├── SecurityHeaders middleware     CSP (nonce + strict-dynamic), HSTS, nosniff…
  ├── config/redirects.php           301s for merged/legacy URLs
  ├── canonical URL shape            /index.php → /, add trailing slash
  ├── Session::start()               secure cookie, idle timeout, flash data
  ├── ad attribution capture         utm_* / gclid → session → saved with the lead
  ├── routes/web.php + routes/admin.php
  ├── VerifyCsrf (all POSTs)
  └── Router::dispatch → Controller → Model/Service → View → HTML
```

Errors: `HttpException` → `resources/views/errors/http.php` with the right status code.
Anything else → logged to `storage/logs/app.log`; a friendly 500 page in production, a trace locally.

## Layers

| Layer | Location | Rule |
|---|---|---|
| Routing | `routes/` | Paths carry trailing slashes; `{slug}` matches kebab-case, `{id}`/`{linkid}` digits |
| Middleware | `app/Middleware/` | SecurityHeaders, VerifyCsrf, Authenticate, OwnerOnly |
| Controllers | `app/Controllers/`, `admin/Controllers/` | Validate → call model/service → render. No SQL |
| Models | `app/Models/` | All SQL, always prepared statements. Return arrays |
| Services | `app/Services/` | Seo, Schema, Sitemap, Uploader, HtmlSanitizer, FormGuard, Mailer |
| Validation | `app/Validation/Validator.php` | Rule strings; returns field → first message |
| Views | `resources/views/`, `resources/partials/`, `admin/views/` | Escape with `e()`; no queries |
| Config | `config/` | `app`, `database`, `business`, `landing`, `redirects` |

Front-end: one `site.css` + one deferred `app.js` for the public site, `admin.css`/`admin.js` for the admin.
No build step, no framework, no Composer dependencies (decision D-001).

## Content model

Services, areas, projects, reviews, FAQs, blog posts, media, SEO overrides and settings all live in
MySQL and are edited in the admin — no code change is needed for normal content work. Only structural
things (new page types, new routes, NAP values, Ads landing-page copy) require a developer.

## Key conventions

- Page SEO: every controller builds a `Seo` object (title, description, canonical, robots,
  breadcrumbs, JSON-LD, page type for analytics). `seo_metadata` rows override it per path.
- Analytics: `data-track` attributes on CTAs; `app.js` pushes events to `dataLayer`.
- Images: uploads are re-encoded to WebP at 400/800/1600 px; templates emit `srcset` + `sizes`
  with explicit width/height.
- Anti-spam: CSRF + honeypot + signed render timestamp + per-IP rate limit on every public form.
- Nothing above `public/` is web-reachable; a root `.htaccess` protects misconfigured hosts.
