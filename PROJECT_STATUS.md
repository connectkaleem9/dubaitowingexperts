# Project Status

Updated: 2026-09-24 · Maintained by the Project Manager agent · Read `CLAUDE.md` first.

## Where the project is

**The site is LIVE at https://dubaitowingexperts.com** — deployed 2026-09-24 to Hostinger, with the
database created, migrated, seeded and filled with the content approved locally. Admin dashboard
works against the live database. 31 pages crawl clean.

What is left is not code: real photos in the new brand, real project details, first customer reviews,
a GTM container ID and a lead-notification email. All of those need the owner.

| Phase | Status |
|---|---|
| 0. CLAUDE.md, agents, skills | ✅ Done |
| 1. Market, competitor and service research | ✅ Done |
| 2. Keyword research, mapping, Ads plan, negatives | ✅ Done (volumes to be validated in Keyword Planner) |
| 3. Architecture, database schema, migrations, seeds | ✅ Done |
| 4. Design system, templates, components | ✅ Done |
| 5. Public site (home, services, areas, projects, reviews, FAQ, blog, legal, landing) | ✅ Done |
| 6. Forms, anti-spam, lead capture, attribution | ✅ Done |
| 7. Admin dashboard (13 modules) | ✅ Done |
| 8. Security hardening | ✅ Done (checklist in `docs/security.md`) |
| 9. SEO implementation (meta, schema, sitemap, robots, redirects) | ✅ Done |
| 10. Analytics/Ads wiring | ✅ Code done — ⏳ needs a GTM container ID |
| 11. Automated tests + SEO audit | ✅ Done, all green |
| 12. Real content (photos, projects, reviews) | ⛔ Blocked — needs the owner |
| 13. Production deploy (Hostinger, HTTPS, DB, content) | ✅ Done 2026-09-24 |
| 14. Search Console + Google Ads go-live | ⏳ Needs the owner's accounts |

## Verified on 2026-09-24 (live — https://dubaitowingexperts.com)

- `php tests/seo-audit.php https://dubaitowingexperts.com` — **31 pages, 0 errors**
- Every key URL returns 200; `http://`, `www.`, non-slash and `/index.php` each redirect once;
  the retired Battery Jump Start URL still 301s; unknown URLs 404
- Admin login → dashboard → leads → media all work against the live database
- No horizontal overflow at 390/768/1440px; no console errors and no CSP violations
- Security headers all present (HSTS, CSP, nosniff, Referrer-Policy, Permissions-Policy,
  X-Frame-Options, COOP)

### Verified locally before deploying

- `php tests/run.php <url>` — 44/44 passed
- `php tests/admin-e2e.php <url>` — 30/30 checks passed
- `php -l` across every PHP file — 0 syntax errors

### Live environment

| | |
|---|---|
| Host | Hostinger shared (`sg-nme-web1100.main-hosting.eu`), PHP 8.3.33, MariaDB 11.8.9 |
| Project root | `~/dubaitowingexperts` — **outside** the web root |
| Document root | `~/domains/dubaitowingexperts.com/public_html` → symlink to `~/dubaitowingexperts/public` |
| Deploy | `tools/deploy.ps1` over SSH (port 65002, key `~/.ssh/dte_deploy`); backs up code + database first |
| Repository | https://github.com/connectkaleem9/dubaitowingexperts |

Hostinger's CDN shows a "Checking your browser" interstitial to headless automation. Real browsers
and Googlebot are served normally (tested), so run `tools/shots.js` against a local copy, not live.

## Design (added 2026-09-22)

The owner's homepage design (`docs/design/reference/dubairecoveryexperts homepage.png`) is
implemented across the whole site — palette, typography, header, hero, all sections and the footer
(decision D-011). Verified with no horizontal overflow at 320/390/768/1024/1440px.

**Artwork received 2026-09-23** and now live: hero background, "Why choose us" background,
"Stuck on the road?" background, header and footer logos, and five service photos (car recovery,
towing, roadside assistance, breakdown recovery, flat tyre). Sources are kept in
`docs/design/reference/`.

**2026-09-24:** the Accident Recovery photo is live, so **all six service pages now have their own
photo**. Four project photos were also supplied and are in the media library, each attached to a
**draft** project (Luxury SUV Recovery, Emergency Roadside Assistance, Luxury Vehicle Recovery, SUV
Breakdown Recovery).

All four projects are **published** (2026-09-24). Their descriptions explain the type of job in the
photo and how that vehicle is handled; they deliberately contain **no invented specifics** — no
dates, customer names, areas, arrival times or prices.

**Worth doing next:** replace each description with the real job details (what happened, which area,
roughly when, where the vehicle was delivered) and set the area + date fields in Admin → Projects.
Specific local detail ranks far better than generic copy and makes the proof genuinely convincing.

**Area pages:** all 30 areas are listed on the home page and the areas hub as pin + name tiles.
Only the five with real local content (Downtown, Marina, Business Bay, Deira, Bur Dubai) have a page
and therefore a link; the other 25 are plain tiles. To turn one into a linked page, write its local
content in Admin → Areas (the admin requires 150+ words so we never publish a thin page).

Three items in the mock-up were deliberately **not** copied (decision D-012): the "30–45 minutes
average response" badge, the "experienced team / modern fleet" claims, and the sample customer
review. The `info@dubaitowingexperts.com` address in the footer appears only once it is confirmed
and saved in Settings.

## Rebrand — 2026-09-24 (decision D-013)

`dubairecoveryexperts.com` could not be registered, so the site is now **dubaitowingexperts.com**
and the business is **Dubai Towing Experts**. Code, content, schema, redirects, robots.txt, brand
keywords and docs are all updated; page titles now read the name from config.

**Blocking before launch — the artwork still shows the old brand:**

1. ~~Logo files~~ — **done 2026-09-24**: the new Dubai Towing Experts logos are live in the header,
   footer, admin and the social share image.
2. **Photos**: the hero image, all six service photos and the four project photos show trucks and
   uniforms branded "DUBAI RECOVERY EXPERTS", and the car-recovery photo shows the old domain on the
   truck door. They need regenerating with the new brand.
3. **Google Business Profile / citations** must use the new name exactly, and the old name should not
   be published anywhere once the rebrand is live.

## Answered by the owner on 2026-09-22 (applied — decision D-010)

- **24/7 availability: yes.** Published on the home page (title, H1, trust list, "why us"), the contact
  page title, CTA blocks, landing pages and FAQ; schema now carries `openingHours Mo-Su 00:00-23:59`;
  the 24-hour ad group is enabled and ads may run at all hours.
- **Coverage: Dubai only.** The areas FAQ now says so explicitly, and the other emirates are
  account-level negative keywords so budget is not spent outside the service area.
- **Response time: varies too much to promise.** The site never states a time; it says we give an
  honest arrival estimate on the call. Ad copy may not contain arrival times.

## Open questions for the owner (blocking content, not code)

These are the only things that cannot be decided from the brief. Everything else has been decided and
recorded in `docs/decisions.md`.

1. **Which services do you actually provide?** (Still open.) Six are live: car recovery, towing,
   roadside assistance, breakdown recovery, accident recovery and flat tyre assistance.
   (Battery jump start was removed on 2026-09-23 at your request.) Do you also offer: fuel delivery,
   lockout help, basement pull-out, desert/sand recovery, motorbike recovery, car transport, heavy
   vehicle recovery? Basement pull-out in particular has real Dubai search demand.
2. **Business facts for local SEO:** public address (or confirm service-area only), business email,
   licence/registration details you are happy to publish, years in operation, fleet (how many trucks,
   flatbed or wheel-lift), insurer partnerships.
3. **Payment:** which methods do you accept, and is payment on completion? (Common FAQ we cannot answer.)
4. **Photos**: real photos of your trucks and jobs — now the single biggest gap, because the design
   is built around them (hero, service cards, area cards, projects). Also confirm the email address
   and your social profile links (Facebook / Instagram / YouTube / LinkedIn) shown in the design.
5. **Accounts to create/share:** Google Business Profile, Google Tag Manager, Google Analytics 4,
   Google Ads, Search Console, and the hosting + domain login.
6. **Lead notification email** to receive enquiries. `MAIL_TO` in the server's `.env` is currently
   empty, so no enquiry emails are sent — leads are still captured and visible in
   Admin → Contact Requests, but nobody is alerted.
7. **Admin account email**: the owner login was created as `yasiiikhan427@gmail.com`. Change it (and
   the password) in Admin → Admin Users if it should belong to someone else.

## Next tasks (in order)

1. Get answers to the questions above; publish/unpublish services and areas accordingly.
2. Add real photos via Admin → Media; set a hero/service image per page.
3. Add 3–6 real projects and gather first reviews.
4. Create GTM + GA4 + Ads accounts; add the GTM ID in Settings; verify every event in Preview.
5. Deploy to staging (`FORCE_NOINDEX=true`), run all suites against it, plus device and Lighthouse testing.
6. Verify Google Business Profile; add the Maps link in Settings.
7. Production deploy following `docs/deployment.md`; submit the sitemap in Search Console.
8. Validate keyword volumes in Keyword Planner; finalise budgets; launch Ads after the §50 checklist.
9. Write the next area pages in priority order (Al Barsha, Al Quoz, JVC, Silicon Oasis, International
   City, Al Qusais, Jebel Ali) — only where coverage is confirmed and genuinely local content exists.

## Known gaps / deliberate omissions

- No admin password reset by email (an owner resets it in Admin → Users). Documented in `docs/security.md`.
- No two-factor authentication yet.
- Lead notification uses PHP `mail()`; switch to SMTP if the host's deliverability is poor.
- No image gallery lightbox (kept simple for performance); can be added if needed.
- Arabic version not built. Worth considering later — it would need `hreflang` and a translation workflow.
- Review/AggregateRating schema is deliberately **not** emitted (Google disallows self-serving review
  markup); reviews are still shown to visitors.
