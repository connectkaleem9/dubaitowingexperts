# Testing

Owner: QA/Testing agent · Updated 2026-09-22

## Suites

| Command | Covers |
|---|---|
| `php tests/run.php` | Validation rules, HTML sanitiser (stored XSS), helpers, SEO/schema builders, sitemap rules, SQL identifier guard, seeded content quality |
| `php tests/run.php <base-url>` | …plus HTTP: status codes, redirects, security headers, CSRF rejection, honeypot, lead + review submission, XSS escaping, admin auth |
| `php tests/seo-audit.php <base-url>` | Crawls every internal link: titles, descriptions, duplicates, H1 count, heading order, canonicals, robots, JSON-LD, alt text, phone/WhatsApp presence, broken links, sitemap and robots.txt |
| `php tests/admin-e2e.php <base-url> <email> <password>` | Admin sign-in, project CRUD with image upload, publish/unpublish, public visibility, review moderation, settings save, role restrictions, activity log |
| `node tools/shots.js <base-url> <paths…> --widths=…` | Renders each page at each width with real mobile emulation, fails on horizontal overflow and saves full-page screenshots to `tools/shots/` |

Never run the HTTP suites against production — they create and delete records.

## Run: 2026-09-22 (after the design implementation — local, PHP 8.3.33, MariaDB 11.4.4)

| Suite | Result |
|---|---|
| `tests/run.php` (unit + HTTP) | **44 / 44 passed** |
| `tests/seo-audit.php` | **28 pages, 28 links, 0 errors, 0 warnings** |
| `tests/admin-e2e.php` | **30 / 30 checks passed** |
| `tools/shots.js` at 320/390/768/1024/1440px | **no horizontal overflow on any page** |
| `php -l` on every PHP file | **0 syntax errors** |

Notable behaviours confirmed:

- POST without a valid CSRF token → 419; honeypot/too-fast submissions are dropped silently (the bot
  still sees a thank-you page, nothing is stored).
- A valid enquiry is stored with hashed IP and campaign attribution, and the thank-you page fires the
  conversion event.
- A submitted review is stored as `pending` and is not visible publicly until approved.
- `<img src=x onerror=…>` in review text renders escaped.
- Uploaded JPEGs are re-encoded to WebP at 400/800/1600 px with EXIF removed, and served with `srcset`.
- Editors are blocked (403) from Settings/Users/Activity; owners are not.
- Unpublished projects and areas return 404 and stay out of the sitemap.
- The public form rate limit really fires (5 leads / 10 minutes per IP); the suite clears the
  counters first so repeated runs are not blocked by it.
- Schema publishes `openingHours` only from the confirmed setting, and never an address or rating.

## Not yet done (needs the live/staging site or the owner)

- [ ] Real-device testing: iPhone Safari, Android Chrome — tap targets, sticky CTA, WhatsApp handoff.
- [x] Automated responsive check at 320/390/768/1024/1440 px (`tools/shots.js`) — extend to
      375/414/1280/1920 and eyeball the screenshots when real photography is in place.
- [ ] Lighthouse mobile run (LCP/CLS/INP) on staging with real images in place.
- [ ] Screen-reader pass (NVDA or VoiceOver) over the home page, a service page and the lead form.
- [ ] Rich Results Test on one page of each type.
- [ ] Email deliverability test for lead notifications on the production host.
- [ ] GTM Preview verification of every tracked event (needs a container ID).
- [ ] Cross-browser check: Chrome, Safari, Edge, Firefox, Samsung Internet.

## Regression rule

Run all four suites before any deployment, and after any change to forms, auth, uploads, routing or
templates. A failing check blocks release.
