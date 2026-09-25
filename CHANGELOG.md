# Changelog

All notable changes to this project. Newest first.

## 2026-09-25 (night) — Google Analytics and Search Console

### Live
- Analytics is running on **G-8X5K0KMRH2** and Search Console ownership is verified by meta tag.
  Confirmed in a real browser: `page_view` reaches Google with `gcs=G100` before consent and
  `gcs=G111` after Accept, and `phone_click`, `whatsapp_click` and `form_start` all arrive with
  their `link_location` / `form_name` parameters. No console errors, no CSP violations.

### Fixed
- **The cookie banner only appeared when Tag Manager was configured.** A site running Analytics
  alone would therefore never offer it, consent would stay denied for every visitor, and Google
  would receive nothing but cookieless pings. It now appears whenever either tag is set.

### Added
- **Google Analytics 4 can now run on its own**, with just a `G-…` measurement ID and no Tag
  Manager container to build. `app.js` sends every tracked event (phone taps, WhatsApp taps, form
  starts and submissions, quote requests) straight to `gtag` as well as to the data layer, so
  conversions are recorded either way. Setting a GTM container as well still works — they read the
  same data layer — but do not also create a GA4 tag inside GTM or events are counted twice.
- **Google Search Console verification**: paste the value from Google's HTML-tag method into
  settings and it is rendered as `<meta name="google-site-verification">` on every page.
- Both live in Admin → Site Settings, with validation that rejects a whole meta tag pasted in by
  mistake or an ID in the wrong format.

### Notes
- Consent Mode v2 still runs **before** either tag loads, which is what Google requires, and the
  cookie banner keeps controlling it.
- Nothing is sent anywhere until the IDs are saved: with both blank, no third-party script loads.

## 2026-09-25 (evening) — Admin cut down to Projects and Reviews

### Changed
- **The admin menu is Projects and Reviews only.** Signing in lands on Projects. The other modules
  keep working on their own URLs; they are simply not in the menu. *Change password* sits in the
  sidebar footer, because losing that would lock the owner out of their own account.
- **The add/edit project form is title, service, area, date, status and two photos.** The URL slug
  is made from the title, and the card photo follows the "after" shot, so removing the featured
  field changed nothing on the website.
- **The "…or choose from media library" picker is gone** from every image field; images are
  uploaded. The library itself is unchanged and still holds everything.
- Admin sign-in email changed to `info.dubairecoveryexperts@gmail.com`.

### Removed
- The project gallery: with its form section gone, its routes, controller actions and the two model
  writers behind them were unreachable. Galleries already saved still show on the project page.

### Notes
- A project with no description is a thin page, so **project pages are now `noindex,follow` unless
  they carry text**, and those URLs are left out of the sitemap (CLAUDE.md §6). The listing at
  `/projects/`, the home page slider and the cards are unaffected — that is where the photos earn
  their keep. Add a description to a project in the database and its page becomes indexable again.

## 2026-09-25 (later) — Hero photo blends in, reviews published

### Changed
- **The hero photo's bottom edge now dissolves into the navy** instead of stopping at a hard line,
  on the home page and on service and area pages, so the picture belongs to the section rather than
  sitting above it.
- **The top bar is centred on phones**, where only two items are shown.
- **The Pause control is no longer part of the visual design.** It is still there and still works —
  it sits off-screen and appears as soon as it is focused, the way the skip link does. WCAG 2.2.2
  wants a way to stop content that moves on its own, and pausing on hover does nothing for someone
  on a phone or using a keyboard.

### Added
- The owner's five reviews are **approved and live**, on the home page carousel and `/reviews/`.

### Fixed
- **Service and area heroes had lost their photo on phones.** Two later rules were quietly winning:
  `.hero--bg`'s light desktop panel (a `background` shorthand, which resets the image) and
  `.hero--compact`'s padding shorthand (which removed the room the photo band needs). The panel is
  now scoped to 900px and up, and the phone rule carries enough specificity to hold its padding.

## 2026-09-25 — Mobile hero to the owner's reference, form alignment, first reviews

### Changed
- **Mobile hero rebuilt to the reference the owner sent**: the complete photo as a clean band
  across the top — nothing over it, nothing cropped — and the copy below it on solid navy. The
  earlier attempts kept the split but left the copy on a light panel, which is what made the
  section look unfinished; matching the panel to the photo is what ties the two together. Call and
  WhatsApp now share one row across the full width. Service and area heroes follow the same shape,
  using each photo's own aspect ratio.
- **Form fields in a two-column row now line up.** A `.field` stretched to match the taller column
  beside it and the spare height landed on the input, so a select sat lower and taller than the
  text box next to it. Fixes every two-column row on the site, not just the review form.

### Added
- **The owner's five customer reviews** (`customer_reviews.txt`) loaded into the live database as
  **pending**. Approving a review publishes it, and that stays the owner's decision in
  Admin → Reviews. Two carry an admin note: one describes a battery jump start, a service removed
  from the site at the owner's request, and one mentions the Dubai–Abu Dhabi highway, while the
  site states coverage is Dubai only.

### Removed
- `hero-bg-mobile.webp` and `tools/make-hero-mobile.php` — the portrait crop they existed for is
  not needed now that the phone hero shows the photo whole.

## 2026-09-24 (night) — Mobile hero photo and a shorter review form

### Changed
- **The mobile hero now uses a portrait crop of the hero photo** (`hero-bg-mobile.webp`, generated
  from the same original). The source is 1920x800, far too wide for an upright phone hero: `cover`
  was throwing away most of its width and leaving an unrecognisable close-up of the cab. The new
  image keeps the whole truck, with sky above it for the headline. The scrim is heaviest at the top
  and lightens towards the bottom so the truck stays visible, and the copy ends above it.
- Hero stat pills are dark on phones instead of translucent white, and the marquee's edge fade is
  switched off there — it fades to the light page surface and was painting white bands down both
  sides of the strip now that the hero behind it is a photo.
- **The review form no longer asks for a photo or a consent tick.** Consent is given by sending the
  form, with the wording in plain sight beside the button and still recorded in `consent_at`.
  Older reviews that already have photos still show them, in the admin and on the page.

## 2026-09-24 (late evening) — Reviews page and mobile hero

### Changed
- **Mobile hero rebuilt**: the photo now fills the whole hero with the headline, copy and buttons
  on top of it, instead of sitting above the text as a separate band. A navy scrim keeps every
  piece of text past 4.5:1 against the brightest part of the sky, and the accent red is lightened
  (`--c-red-on-dark`) so "in Dubai" stays legible. Service and area heroes get the same treatment.
- **Reviews page rebuilt to the approved design**: a card grid (quote mark, stars, the review,
  then the person) followed by a centred "Leave Us a Review" section. The rating stars and the
  submit button are red, as drawn.
- **Every approved review now shows on one page — paging is gone.** However many reviews there
  are, none is hidden behind a page number (`Review::allApproved()`).
- **Home page review card** matches the design: initials/photo on the left, name, stars and the
  quote beside it, with the existing arrows and dots.

### Added
- `App\Services\SampleContent` with `?preview=sample` — a **signed-in admin** can see the reviews
  page and home page filled with sample reviews to judge the design. The response is noindex, the
  page says plainly that it is a preview, and nothing is written to the database. Invented reviews
  are never published: they mislead visitors and breach Google's review policies (decision D-016).

## 2026-09-24 (late) — Owner's layout changes (decision D-015)

### Changed
- **The breadcrumb trail ("Home / Services / Car Recovery") is gone from every page.** The
  BreadcrumbList JSON-LD stays exactly as it was — that is what Google reads for breadcrumb rich
  results — so nothing is lost in search.
- **Service and area heroes now use the page's own photo as the hero background**, given the same
  treatment as the home page: the complete photo full width on phones, and a right-hand layer
  dissolving into the copy panel from 900px up. The photo is no longer a card beside the text.
  It is preloaded (`$seo->preloadImage`), because a CSS background is discovered only after the
  stylesheet parses and would otherwise delay the largest paint.
- **The site header and footer now wrap the admin**, on the sign-in page and on every dashboard
  screen, so the admin sits inside the same brand as the website.

### Removed
- `resources/partials/breadcrumbs.php` and its CSS — nothing rendered it any more.

## 2026-09-24 (night) — Live on dubaitowingexperts.com

The site is deployed and running on Hostinger. Document root is a symlink to `public/`, so no PHP
source is web-reachable. Database created, migrated and seeded; the owner's admin account exists.

### Added
- **Content moved from local to live**: 21 media items with their files, 6 services, 30 areas,
  4 projects, 15 FAQs, 3 blog posts and the site settings — the site now looks live exactly as it
  was approved locally.
- `tools/export-content.php` — dumps the editable content tables to SQL for moving a database
  between environments. Accounts, logs, leads and throttling data are never exported, and
  **reviews are excluded on purpose**: real reviews only exist in production, and a development
  database collects test rows from `tests/admin-e2e.php` that must never reach the live site.
- `public/favicon.ico` (16/32/48) — browsers request `/favicon.ico` regardless of the `<link>`
  tags, and it was returning 404.

### Fixed
- **The Content-Security-Policy was being thrown away in production.** Hostinger's LiteSpeed
  replaces the header PHP sends with its own `upgrade-insecure-requests`, so the site was live with
  no CSP at all. The nonce-based policy is now emitted as a `<meta http-equiv>` tag, which no host
  can rewrite, and `.htaccess` sends `frame-ancestors` (the one directive meta cannot carry).
  Decision D-014. Verified live: the full policy is enforced and no page reports a violation.
- `database/create-admin.php` crashed on hosts that disable `shell_exec` (Hostinger does). It now
  falls back to reading the password without hiding it instead of failing.
- `tools/deploy.ps1` broke when the SSH key path contained a space (`C:\Users\One Click\...`) —
  the remote-install arguments are now quoted.

### Verified live
- `tests/seo-audit.php` — **31 pages crawled, 0 errors**.
- All key URLs 200; `http://`, `www.`, non-slash and `/index.php` each redirect once; the removed
  Battery Jump Start page still 301s to Roadside Assistance; unknown URLs 404.
- Admin login, dashboard, leads and media all work against the live database.
- No horizontal overflow at 390/768/1440px; no console errors or CSP violations.
- Security headers present: HSTS, CSP, `X-Content-Type-Options`, `Referrer-Policy`,
  `Permissions-Policy`, `X-Frame-Options`, `Cross-Origin-Opener-Policy`.

### Notes
- Hostinger's CDN shows a "Checking your browser" interstitial to headless automation. Real
  browsers and Googlebot are served normally (tested), but automated screenshot tooling must be
  pointed at a local copy.
- `MAIL_TO` is empty, so lead emails are not sent yet — leads are still stored and visible in
  Admin → Contact Requests. Set the owner's email in `.env` to enable notifications.

## 2026-09-24 (night) — Deployment tooling

### Added
- `tools/deploy.ps1` — one-command SSH deploy: builds a clean release (no `.env`, docs, tools, tests
  or local media), uploads a single archive, extracts it on the server, keeps `.env` and
  `public/uploads`, fixes permissions and runs migrations. Backs up code **and** database to
  `../dte-backups/` first, and smoke-tests the live URLs afterwards. `-DryRun` builds only.
- `tools/make-env.ps1` — generates the production `.env` with a fresh 64-char `APP_KEY`; the DB
  password is typed at the prompt, never stored in a script. Output lives in git-ignored `deploy/`.
- `docs/deployment.md` — SSH deploy flow, server prerequisites and rollback steps.
- Deployment SSH key generated at `~/.ssh/dte_deploy`; its public key goes in the server's
  `authorized_keys`.

## 2026-09-24 (evening) — Domain and business rename (decision D-013)

### Changed
- Domain is now **dubaitowingexperts.com** everywhere: canonical host and HTTPS/non-www redirects,
  robots.txt sitemap line, default `APP_URL`, mail sender, schema and all documentation.
- Business renamed to **Dubai Towing Experts** across page titles, schema, WhatsApp pre-filled
  messages, service/area content, media alt text, settings, brand keywords and docs.
- Page titles now read the name from `config/business.php` (`business('name')`) instead of a
  hard-coded string — a future rename is a one-line change, as CLAUDE.md §2 requires.

### Added (same day)
- **New Dubai Towing Experts logo artwork** imported: header version (trimmed from its white
  background) and footer version (transparent), both WebP. Header height raised to 78px (56px on
  phones) so the "TOWING EXPERTS" line stays legible, with the header bar at 96px.
- The Open Graph share image, admin sidebar and admin login now use the new brand too; the old
  placeholder `logo.svg` was deleted and schema `logo` points at the real artwork.

### Still needs the owner
- **New photos**: the supplied truck photos are branded with the old name, and one shows the old
  domain on the truck door.

## 2026-09-24 (later) — Projects slider and reviews carousel

### Changed
- Home page **projects** are now a continuous slider of compact tiles (photo, title, area/service)
  with its own Pause/Play control, matching the approved design.
- Home page **reviews** are now a carousel — one review at a time with previous/next arrows and
  dots, wrapping, and arrow-key support. With no approved reviews it still shows the invitation card.
- Section headings in that two-column block keep their "View all …" link on the same line.
- Review captions no longer start with a stray separator when the service/area fields are empty.

## 2026-09-24 — Accident Recovery photo and project photos

### Added
- **Accident Recovery service photo** imported and linked — every service page now has its own image.
- Four **project photos** imported into the media library and **published as projects** (Luxury SUV
  Recovery, Emergency Roadside Assistance, Luxury Vehicle Recovery, SUV Breakdown Recovery), each
  linked to its service. Descriptions cover the type of job and how the vehicle is handled, with no
  invented dates, areas, names or prices (CLAUDE.md §2) — the owner can add the real details later.
- Projects listing: card headings are now `h2`, fixing an H1 → H3 jump flagged by the SEO audit.

## 2026-09-23 (late) — Mobile sliders and hero background

### Changed
- **Mobile hero** now uses the photo as the section's background image at full width and full
  strength, so the complete picture is visible (no crop, no wash) with the copy beneath it.
- **Hero stats (24/7 · Clear price · Careful handling · All over Dubai)** slide right → left on
  phones; from 900px up they are the usual static row.
- **"Why choose us" six points** slide right → left on phones; unchanged six-across grid on desktop.
- Each slider has its own Pause/Play control; the hazard strip stays removed.

## 2026-09-23 (evening) — Hero clean-up and navigation

### Changed
- Hero now has two CTAs only: **Call Now** and **WhatsApp Now** ("Request a Quote" removed).
- The red/navy hazard strip was removed from the hero **and from the Ads landing pages**.
- **Mobile hero**: the complete photo is now a full-width banner at the top of the hero, with the
  headline, copy, buttons and stats beneath it — nothing cropped or washed out.
- **Header navigation trimmed**: FAQ and Blog removed from the top menu (they remain in the footer
  Quick Links, which also gained Reviews and Contact), keeping the main nav short on laptops.

## 2026-09-23 (later) — Hero, slider and areas refinements

### Fixed
- **Services slider looked like a static row with a scrollbar** for anyone whose OS has "reduce
  motion" enabled: the global reduced-motion rule was killing the animation and the fallback turned
  the row into a scroller. The marquee is now excluded from that rule, slows to 60s instead of
  stopping, never shows a scrollbar, and has a visible **Pause/Play button** (WCAG 2.2.2).
- `tools/shots.js` was screenshotting cached CSS, which hid layout changes — it now disables the
  network cache before each capture.

### Changed
- **Hero rebuilt to the supplied reference**: photo on the right dissolving into a clean light panel,
  copy and the four stats clear of the truck, three CTAs on one row, and the "Anytime, anywhere"
  script line back over the sky.
- **Areas section now lists every area we cover** as pin + name tiles. Areas with a written page
  link to it; the rest are plain tiles (no dead links, no thin pages). The `/areas/` hub gained a
  matching "We also cover" list.

## 2026-09-23 — Owner artwork and section changes

### Added
- Owner's photography imported and optimised: hero background, "Why choose us" background,
  "Stuck on the road?" card background (WebP, 38–158 KB each with contrast-safe overlays) and five
  service photos into the media library, linked to their service pages.
- Owner's logo artwork in the header (navy) and footer (white), replacing the drawn placeholder mark.
- **Services row is now a continuous auto-slider**, moving left → right, pausing on hover/focus,
  duplicated track for a seamless loop, and replaced by a swipeable row under `prefers-reduced-motion`.

### Changed
- **Areas section shows place names only** — no images, per the owner's instruction. Tiles flow to
  fill the row whatever the number of published areas.
- **How It Works** rebuilt to the supplied reference: full-width row of three steps with the red
  "Need immediate help?" card beside the heading, titles no longer wrapping.
- Hero stats sit on a translucent panel so every label stays legible over the photo.

### Removed
- **Battery Jump Start** service (owner's instruction) — deleted from the database and the seed file,
  with a 301 from `/services/battery-jump-start/` to `/services/roadside-assistance/`.

## 2026-09-22 — Homepage design implemented (decisions D-011, D-012)

### Added
- The owner's design (`docs/design/reference/`) is now the site's look: navy top bar, white sticky
  header with dropdown navigation and a red phone button, gradient hero with skyline, stat row and
  hazard-chevron divider, six-up service cards, navy "Why choose us" band, three-step section with a
  red "Need immediate help?" card, area photo cards, projects + reviews columns, FAQ accordion beside
  the blue "Stuck on the road?" card, and the five-column footer.
- Self-hosted Poppins (latin subset, 5 weights, 38 KB) — no third-party font request.
- New tow-truck logo, favicon, Apple touch icon and Open Graph image in the new palette.
- **Battery Jump Start** service page (included in the design) with original content.
- Homepage hero photo slot: Settings → *Homepage hero photo*, so the owner's truck photo drops into
  the hero exactly as drawn. Service and area cards do the same with their own images.
- `tools/shots.js` — screenshots every page at chosen widths with real mobile emulation and fails on
  horizontal overflow; used to verify 320–1440px.

### Changed
- Unverifiable claims in the mock-up were replaced rather than copied (see decision D-012):
  no "30–45 minutes average response", no fleet/experience claims, no sample review, no unconfirmed
  email address.
- Photography-dependent components degrade to branded placeholders until real photos are uploaded.

## 2026-09-22 — Owner answers applied (decision D-010)

### Changed
- **24/7 availability confirmed** and published: home page title/H1/trust list/"why us", contact page
  title, CTA blocks, all three Ads landing pages, a new FAQ, and schema `openingHours Mo-Su 00:00-23:59`.
  The 24-hour ad group is enabled and ads may now run at all hours.
- **Coverage stated as Dubai only**: areas FAQ updated; other emirates added as account-level negative
  keywords in `docs/seo/negative-keywords.md`.
- **Response time**: no fixed time is promised anywhere. New FAQ explains we give an honest arrival
  estimate on the call; ad copy rules forbid arrival-time claims.
- `CLAUDE.md`, keyword research/map, Google Ads plan and local SEO docs updated to match.

## 2026-09-22 — Initial build

### Added
- **Project foundation**: `CLAUDE.md` (operating rules), 15 agent definitions in `.claude/agents/`,
  20 reusable skills in `.claude/skills/`, decision log (`docs/decisions.md`).
- **Research**: Dubai recovery market and competitor analysis (10+ competitors reviewed), service
  research with a cannibalisation review, UAE terminology and accident-reporting context.
- **SEO planning**: keyword universe with intent and priority, keyword→URL map, Google Ads keyword
  plan, validated negative-keyword list, on-page/technical/local SEO specs, internal link map,
  schema map, redirect map, conversion-tracking plan.
- **Application**: custom PHP MVC (router with trailing-slash canonicalisation, request, view,
  PDO wrapper, session, CSRF, auth), 13 models, services for SEO, schema, sitemap, uploads, HTML
  sanitising, anti-spam and mail.
- **Public site**: home, services hub + 6 service pages, areas hub + 5 area pages, projects, reviews
  (with moderated submission form), FAQ, blog + 3 guides, about, contact, 4 legal pages, thank-you,
  3 Google Ads landing pages, dynamic `sitemap.xml`, `robots.txt`, 404/500 pages.
- **Design system**: navy + safety-amber theme, chevron motif, mobile-first CSS with tokens, sticky
  mobile call/WhatsApp bar, accessible forms, inline SVG icon sprite, generated logo/favicon/OG image.
- **Conversion features**: context-aware WhatsApp pre-filled messages, "Send my location" (geolocation
  → map pin in WhatsApp), lead/quote forms with campaign attribution (UTM + GCLID), GTM + Consent
  Mode v2 wiring and `dataLayer` events for calls, WhatsApp, forms and page types.
- **Admin dashboard**: dashboard with setup warnings, contact requests (pipeline, notes, CSV export),
  projects CRUD with image upload and gallery, review moderation, services, areas (with a thin-page
  guard), FAQs, blog, media library with alt-text editing, per-path SEO overrides, site settings,
  admin users with roles, activity log, security page with password change and live checks.
- **Database**: 16-table schema with foreign keys and indexes, migration runner, idempotent seeder,
  admin creation CLI.
- **Security**: prepared statements, CSRF everywhere, Argon2id passwords, login throttling and
  lockout, session hardening, allow-list HTML sanitiser, nonce-based CSP, secure upload pipeline
  (finfo + GD re-encode + EXIF strip), honeypot/time-trap/rate limits, hashed IPs, activity audit log.
- **Tests**: `tests/run.php` (42 unit + functional + security tests), `tests/seo-audit.php`
  (site crawler and on-page SEO audit), `tests/admin-e2e.php` (29 admin workflow checks).
- **Documentation**: README, architecture, database, security, deployment, testing, analytics,
  design system, and the SEO/research document set.

### Notes
- All services and the five area pages are seeded as published; every factual claim is limited to what
  the brief confirms. No hours, prices, response times, licences or ratings are published anywhere.
- Projects and reviews are intentionally empty — they must contain real jobs and real customer reviews.
