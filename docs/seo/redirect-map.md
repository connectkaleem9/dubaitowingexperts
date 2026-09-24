# Redirect Map

Owner: Technical SEO agent · Source of truth: `config/redirects.php` · Updated 2026-09-22

| Old path | New path | Type | Reason |
|---|---|---|---|
| `/services/vehicle-recovery/` | `/services/car-recovery/` | 301 | Same search intent, merged to avoid cannibalisation (decision D-003) |
| `/services/vehicle-recovery` | `/services/car-recovery/` | 301 | Non-slash variant |
| `/services/battery-jump-start/` | `/services/roadside-assistance/` | 301 | Service removed at the owner's request (2026-09-23); roadside assistance is the closest page |
| `/home/` | `/` | 301 | Common legacy path |
| `/index.html` | `/` | 301 | Legacy static site path |

Handled automatically (no entry needed):
- any extension-less path without a trailing slash → same path with a slash (301)
- `/index.php` → `/` (301)
- `http://` and `www.` → `https://dubaitowingexperts.com` (301, in `public/.htaccess`)

## Rules

1. One hop only. If A → B and B later moves to C, update A to point at C as well.
2. Add a redirect **in the same change** as any slug rename or page removal.
3. Never redirect a removed page to the home page if a closely related page exists — send it there instead.
4. After adding redirects, re-run `php tests/seo-audit.php <url>`; it warns when an internal link
   points at a redirecting URL so the links themselves can be updated.

## If the domain previously had a website

Before launch, collect the old URLs (Search Console, Analytics, a crawl of the old site or
web.archive.org), map each one to its closest new page, and add them here and in
`config/redirects.php`. Unmapped old URLs should 404 (not soft-404 to the home page).
