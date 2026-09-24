# CLAUDE.md — Dubai Towing Experts

This is the operating manual for every Claude session and agent working on this project.
Read it first, then read `PROJECT_STATUS.md`, then the docs relevant to your task.

The original brief is `DubaiRecoveryExperts_Master_Website_Plan (2).txt` (the "Master Plan").
If this file and the Master Plan disagree, the Master Plan wins unless a deviation is recorded
in `docs/decisions.md`.

---

## 1. Project identity

A Local SEO + Google Ads **lead-generation** website for a vehicle recovery business in Dubai.
It is not a brochure site. Every page exists to answer a real search intent and convert the
visitor into a phone call, WhatsApp message or form lead.

Primary goals, in priority order:
1. Generate qualified recovery-service leads (call, WhatsApp, form).
2. Rank organically for Dubai recovery searches.
3. Strong Local SEO foundation (NAP consistency, LocalBusiness schema, area pages).
4. Google Ads-ready landing pages with conversion tracking.
5. Scalable service and area pages managed from the admin.
6. Secure admin dashboard for projects, reviews, leads, FAQs, services, areas, media, SEO.
7. Fast, mobile-first, conversion-focused.

## 2. Business information (single source of truth)

| Field | Value | Notes |
|---|---|---|
| Business name | Dubai Towing Experts | Exact spelling everywhere |
| Domain | dubaitowingexperts.com | Canonical host: `https://dubaitowingexperts.com` (no www) |
| Phone (display) | 052 585 1934 | |
| Phone (link) | `tel:+971525851934` | See decision D-002 |
| WhatsApp | `https://wa.me/971525851934` | |
| Location | Dubai, UAE | No street address has been provided |
| Availability | 24/7 (`Mo-Su 00:00-23:59`) | Confirmed by the owner 2026-09-22 (D-010) |
| Coverage | Dubai only | Confirmed 2026-09-22 — do not imply other emirates |
| Primary market | Dubai | |

These values live in `config/business.php` and are copied into the `settings` table.
**Never hardcode them in views** — use the `business()` / `setting()` helpers.

**Unknown — do not invent:** street address, years in business, fleet size, certifications,
licenses, prices, **response/arrival times** (the owner says they vary too much to promise —
always phrase as "we'll give you an estimate when you call"), service guarantees, review counts,
ratings, coverage outside Dubai. Where copy needs one of these, write around it or leave a
`{{OWNER_TO_CONFIRM}}` marker that the QA agent will block from going live.

## 3. Technology stack

- PHP 8.1+ (no framework; small custom MVC), Composer-free PSR-4 autoloader.
- MySQL 8.0+ / MariaDB 10.6+, `utf8mb4_unicode_ci`, InnoDB, PDO only.
- HTML5, CSS3 (custom properties, no CSS framework), vanilla JavaScript (no jQuery).
- Apache with `.htaccess` (typical cPanel host) — Nginx rules documented in `docs/deployment.md`.
- Document root is `public/`. Nothing outside `public/` may be web-reachable.

## 4. Folder architecture

```
app/            Core + public-site code (namespace App\)
  Core/         Router, Request, Response, View, Database, Session, Csrf, Auth, Config
  Controllers/  Public controllers
  Models/       Data access (all SQL lives here)
  Services/     Business logic (Seo, Schema, Sitemap, Mailer, Uploader, Tracking)
  Helpers/      Global helper functions (e(), url(), asset(), setting(), business())
  Middleware/   AuthMiddleware, CsrfMiddleware, RateLimit, SecurityHeaders
  Validation/   Validator + rules
admin/          Admin module (namespace Admin\) — Controllers/ and views/
config/         app.php, database.php, business.php (.env-driven, no secrets committed)
database/       migrations/*.sql, seeds/*.sql, migrate.php
routes/         web.php (public), admin.php
public/         index.php front controller, .htaccess, robots.txt, assets/, uploads symlink target
resources/      views/ (page templates), partials/ (reusable components)
storage/        uploads/ (served via controlled route or public/uploads), logs/, cache/
docs/           research/, seo/, design/, architecture.md, database.md, security.md, ...
tests/          PHP test scripts + SEO crawler checks
.claude/        agents/ and skills/
```

Do not restructure without recording a decision in `docs/decisions.md`.

## 5. Coding standards

- `declare(strict_types=1);` in every PHP file. PSR-12 style. 4-space indent.
- Classes `PascalCase`, methods `camelCase`, DB columns `snake_case`, URLs `kebab-case`.
- Controllers stay thin: validate → call Model/Service → render view.
- All SQL lives in Models, always via prepared statements with bound parameters.
  Never interpolate variables into SQL. Whitelist column names for ORDER BY / filters.
- Views receive data only; no queries in views.
- Every echo of dynamic data uses `e()` (htmlspecialchars, ENT_QUOTES, UTF-8).
  JSON-LD uses `json_encode` with `JSON_HEX_TAG|JSON_HEX_AMP|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE`.
- Reusable components go in `resources/partials/`. Do not copy large markup blocks.
- JavaScript: one small deferred `app.js` (+ `admin.js`). No inline event handlers
  (keeps a strict CSP possible). Tracking via `data-track` attributes.
- CSS: one `site.css` built from design tokens; mobile-first `min-width` media queries.
- No dead code, no commented-out blocks, no debug output in committed code.

## 6. SEO standards

Every indexable page must have: unique `<title>` (≤ 60 chars target), unique meta description
(≤ 155 chars target), self-referencing absolute canonical, exactly one H1, logical H2/H3,
OG title/description/image, Twitter card, BreadcrumbList (except home), internal links,
descriptive alt text, a visible CTA, phone and WhatsApp.

- Clean trailing-slash URLs: `/services/car-recovery/`. Non-slash and `index.php` variants 301.
- One URL per intent. Keyword → intent → cluster → page. See `docs/seo/keyword-map.md`.
- Never keyword-stuff. Write for the reader; primary keyword in title, H1, intro, naturally.
- No thin area pages. An area page ships only with genuinely area-specific content
  (the `areas.is_published` flag stays off until content passes the uniqueness checklist).
- `noindex` for thank-you, admin, login, search/filter/pagination beyond page 1 where appropriate.
- Sitemap is generated dynamically from published DB records (`/sitemap.xml`).
- SEO metadata per page is editable in admin (`seo_metadata` table) with sensible defaults.

## 7. Google Ads standards

- Each ad group maps to the most relevant existing page (`docs/seo/google-ads.md`).
- Dedicated `/landing/*` pages only when a campaign needs a distraction-free page;
  they are `noindex,follow` to avoid duplicating the SEO service page.
- Landing pages must repeat the ad's promise in the H1, show call + WhatsApp above the fold,
  include a short form, and load fast on mobile.
- Never claim anything in ads or landing pages that is unconfirmed (see §2 unknowns).

## 8. Local SEO standards

- NAP identical everywhere (header, footer, contact, schema, landing pages).
- LocalBusiness schema (`AutomotiveBusiness` subtype, `areaServed: Dubai`) without `streetAddress`.
  `openingHours` is published as `Mo-Su 00:00-23:59` (24/7, confirmed 2026-09-22).
- Area pages link to relevant services and vice versa; see `docs/seo/internal-link-map.md`.

## 9. Security standards

PDO prepared statements; CSRF token on every state-changing form; `password_hash` (Argon2id
where available, else bcrypt); session cookies `HttpOnly`, `Secure`, `SameSite=Lax`,
regenerate ID on login; login throttling; role check on every admin route; input validation
server-side (client-side is UX only); output escaping; uploads: size limit, `finfo` MIME
check, re-encode images with GD, random filenames, no execution in upload dir; security
headers (CSP, X-Content-Type-Options, Referrer-Policy, frame-ancestors, Permissions-Policy);
honeypot + time-trap + rate limit on public forms; credentials only in `.env` (never committed).

## 10. Design standards

Defined in `docs/design/design-system.md` and implemented as CSS custom properties in
`public/assets/css/site.css`. Direction: professional, emergency-ready, trustworthy, modern,
Dubai-focused, conversion-oriented. Mobile sticky call/WhatsApp bar on every public page.

## 11. Content standards

Original, helpful, service- and location-specific, human-readable. Never copy or spin
competitors, invent claims, reviews, projects, statistics, certifications, experience or
guarantees. Reviews and projects come only from the admin (real data).
Placeholder content must be clearly marked and never published.

## 12. Database standards

InnoDB, utf8mb4, `id INT UNSIGNED AUTO_INCREMENT` PKs, `created_at`/`updated_at` timestamps,
foreign keys with explicit ON DELETE behavior, indexes on every FK, slug and status column.
Schema changes only through numbered files in `database/migrations/`. See `docs/database.md`.

## 13. Accessibility standards

WCAG 2.2 AA: semantic landmarks, skip link, keyboard-operable menus, visible focus,
labels on all inputs, errors linked via `aria-describedby`, contrast ≥ 4.5:1 for text,
touch targets ≥ 44px, `prefers-reduced-motion` respected. ARIA only when HTML can't do it.

## 14. Performance standards

Targets (mobile, 4G): LCP < 2.5s, CLS < 0.1, INP < 200ms, TTFB < 600ms.
System font stack (no web-font blocking), critical CSS small enough to inline or a single
cached stylesheet, deferred JS, WebP images with width/height set, `loading="lazy"` below the
fold, `fetchpriority="high"` on the hero image, long cache headers on `/assets/`.
Test widths: 320, 375, 390, 414, 768, 1024, 1280, 1440, 1920.

## 15. Testing standards

Nothing is "done" until tested. Run `php tests/run.php` (unit/functional) and
`php tests/seo-audit.php <base-url>` (crawls the site and checks §6 rules).
Security checklist in `docs/security.md` is re-run after any auth, form or upload change.
Record results in `docs/testing.md`.

## 16. Deployment standards

See `docs/deployment.md`. HTTPS only, `APP_ENV=production`, `APP_DEBUG=false`, errors logged
to `storage/logs/` not displayed, `.env` outside web root, run migrations, verify robots.txt
is not blocking in production, submit sitemap in Search Console.

## 17. Agents

Defined in `.claude/agents/`. The Project Manager agent coordinates; the others own their
domain. Every agent follows this file. Responsibilities are in each agent file and in the
Master Plan §3.

## 18. Skills

Defined in `.claude/skills/<name>/SKILL.md`. Each has Purpose, Inputs, Process, Rules,
Output, Validation checklist. Use the matching skill before doing that kind of work.

## 19. Development workflow (every session)

1. Read `CLAUDE.md` → `PROJECT_STATUS.md` → relevant docs.
2. Check existing implementation before writing anything new.
3. Pick the next task from `PROJECT_STATUS.md`; check its dependencies.
4. Research / update docs if the task needs it.
5. Implement → test → fix.
6. Run SEO / security / performance checks where relevant.
7. Update `CHANGELOG.md` and `PROJECT_STATUS.md`.
8. Continue to the next logical task.

## 20. Documentation requirements

Keep current: `README.md`, `CHANGELOG.md`, `PROJECT_STATUS.md`, `docs/decisions.md`,
`docs/architecture.md`, `docs/database.md`, `docs/security.md`, `docs/deployment.md`,
`docs/testing.md`, `docs/analytics-tracking.md`, `docs/seo/*`, `docs/research/*`, `docs/design/*`.

## 21. Decision-making rules

- Decide implementation details yourself; record non-obvious ones in `docs/decisions.md`
  (ID, date, decision, reason, alternatives).
- Ask the owner **only** for: missing business-critical facts, which services are actually
  offered, legally significant choices, destructive/irreversible actions, or unresolvable
  requirement conflicts. Batch such questions in `PROJECT_STATUS.md` → "Owner questions".
- While waiting, build everything else; mark dependent content as pending, never guess it.

## 22. Core rules (from Master Plan §2)

1. Don't change approved architecture without a documented reason.
2. Don't remove working functionality unnecessarily.
3. Don't overwrite completed work without checking dependencies.
4–9. Don't invent business info, services, reviews, projects, statistics, certifications.
10–12. No keyword stuffing, no duplicate SEO pages, no thin location pages.
13–20. Semantic HTML, secure PHP, prepared SQL, validate + sanitize input, escape output,
protect admin, protect uploads, never expose credentials.
21–29. Mobile-first, fast, canonicals, sitemap, robots, metadata, schema, internal links,
conversion tracking — all maintained.
30–31. Document important decisions; test before calling anything complete.
32–34. Don't ask questions the docs already answer; decide independently; ask only when
genuinely business-critical.
