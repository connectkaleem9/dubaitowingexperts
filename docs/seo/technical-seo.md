# Technical SEO

Owner: Technical SEO agent · Updated 2026-09-22 · Audit: `php tests/seo-audit.php <base-url>`
(crawls every internal link and fails on missing titles/H1s/canonicals, duplicates, broken links,
invalid JSON-LD, missing alt text, and sitemap/robots problems).

## URL structure

- Lowercase, hyphenated, trailing slash: `/services/car-recovery/`, `/areas/dubai-marina/`.
- No query-string page URLs (`?id=` is never used); `?page=N` only for pagination.
- One canonical host: `https://dubaitowingexperts.com` (non-www, HTTPS). Both variants 301 once.
- `/index.php` and extension-less paths without a slash 301 to the canonical form.

## Crawling and indexing

| Path | Directive |
|---|---|
| Public pages with content | `index,follow`, in sitemap |
| `/landing/*` | `noindex,follow`, excluded from sitemap, allowed in robots.txt (AdsBot must fetch them) |
| `/thank-you/` | `noindex,follow`, disallowed in robots.txt |
| `/admin/*` | `noindex,nofollow` header + robots.txt disallow + login wall |
| Empty listing pages | `noindex,follow` until they have content |
| Staging | `FORCE_NOINDEX=true` → `X-Robots-Tag: noindex` on every response |

`robots.txt` also disallows `?utm_` and `?gclid=` URLs for normal crawlers while explicitly allowing
`AdsBot-Google` and `AdsBot-Google-Mobile` everywhere, and points to the sitemap.

## Sitemap

`/sitemap.xml` is generated on request from published content (services, areas, projects, blog posts)
plus the static pages, with `lastmod` from `updated_at`. Utility, landing, admin and `noindex` paths
are excluded automatically, as are listing pages with no content yet.

## Redirects

Managed in `config/redirects.php` and documented in `redirect-map.md`. Rules: one hop only, 301 for
permanent moves, and a redirect must be added whenever a slug changes (the admin reminds the editor
when a service or area is deleted).

## Core Web Vitals

Implementation choices that protect LCP/CLS/INP:

- No web fonts, no CSS/JS frameworks; one stylesheet (~20 KB) and one deferred script (~5 KB).
- Hero content is text — the largest paint has no image dependency on most pages; where a page image
  exists it is `fetchpriority="high"` and not lazy-loaded.
- Every image has explicit `width`/`height` and `srcset`/`sizes`; uploads are WebP at 400/800/1600.
- Below-the-fold images are `loading="lazy"`.
- Long-cache headers on `/assets/` and `/uploads/`; compression via `mod_deflate`.
- GTM loads asynchronously after the nonced bootstrap and is the only third-party request.

Measure with Lighthouse (mobile) after launch and record results in `docs/testing.md`.

## Current audit status (local, 2026-09-22)

27 pages crawled, 27 internal links checked, **0 errors, 0 warnings**.
Re-run against staging and production before launch.
