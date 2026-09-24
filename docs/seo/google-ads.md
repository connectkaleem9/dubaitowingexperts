# Google Ads Plan

Owner: Google Ads agent · Updated 2026-09-22 · Keywords: `google-ads-keywords.md` ·
Negatives: `negative-keywords.md` · Tracking: `../analytics-tracking.md`

## Account structure

| Campaign | Ad groups | Landing page |
|---|---|---|
| Car Recovery Dubai | Car Recovery · Emergency Car Recovery · Near Me · 24 Hour | `/services/car-recovery/` · `/services/breakdown-recovery/` · `/landing/car-recovery-dubai/` |
| Towing Dubai | Towing Service · Car Towing · Emergency Towing | `/services/towing-service/` · `/landing/emergency-towing-dubai/` |
| Roadside Assistance Dubai | Roadside Assistance · Breakdown Assistance · Flat Tyre · *(Battery — if offered)* | `/services/roadside-assistance/` · `/services/flat-tyre-assistance/` · `/landing/roadside-assistance-dubai/` |
| Brand | Dubai Towing Experts | `/` |

Search campaigns only to start. No Display/Performance Max until conversion data exists.

## Settings

- Location: Dubai — **Presence: people in or regularly in your targeted locations** (never "interest").
- Languages: English + Arabic (the site is English; Arabic-speaking users often search in English).
- Devices: all, with a positive mobile bid adjustment — roadside searches are phone-first.
- Ad schedule: all hours. The owner confirmed on 2026-09-22 that calls and WhatsApp are answered
  24/7 (decision D-010), so overnight and weekend searches — which are disproportionately urgent —
  can be served. Review the hourly performance report after a month and bid-adjust by time of day.
- Bidding: Maximise Clicks with a CPC cap for the first ~2 weeks, then Maximise Conversions /
  Target CPA once 15–30 conversions per month are recorded.
- Match types: phrase + exact first. Broad only later, and only with Smart Bidding + tight negatives.

## Assets (extensions)

- **Call asset** with 052 585 1934 and call reporting on (counts calls ≥ 60 s as conversions).
- **Sitelinks**: Car Recovery, Towing, Roadside Assistance, Areas We Cover, Reviews, Contact.
- **Callouts**: only claims that are true — "Price agreed before dispatch", "Call or WhatsApp",
  "Your choice of garage", "Covering all of Dubai".
- **Structured snippets** (Services): Car recovery, Towing, Breakdown recovery, Accident recovery, Flat tyre.
- **Location asset** once the Google Business Profile is verified.

## Ad copy rules

Allowed: service name, "Dubai", area names, "24/7" / "Open 24 Hours" (confirmed 2026-09-22),
"Call Now", "WhatsApp Us", "Quote Before Dispatch", "Your Garage Or Ours", "Tell Us Your Location".
**Not allowed until the owner confirms in writing:** any arrival time ("30 minutes" etc. — the owner
has said it varies too much to promise), any price, "licensed", "insured", star ratings, review
counts, "cheapest", "No. 1", years of experience.

Each ad group's headline 1 should mirror the keyword ("Car Recovery in Dubai"), headline 2 the
differentiator, and the description should state the process (call/WhatsApp → agreed price → delivered).

## Landing pages

`/landing/*` pages are `noindex,follow`, distraction-free (logo + call button only), with the form and
call/WhatsApp buttons above the fold on a 360 px screen. Content lives in `config/landing.php` so new
campaigns can be added without touching templates. A/B test service page vs landing page per ad group.

## Pre-launch checklist (Master Plan §50)

- [ ] Site live on HTTPS, fast on mobile
- [ ] Phone and WhatsApp links work from a real phone
- [ ] Forms deliver (test enquiry received in the admin **and** by email)
- [ ] Thank-you page fires `form_submit` / `quote_request`
- [ ] GTM container live; conversion actions created and verified in Preview
- [ ] Conversion Linker tag active; auto-tagging on in Google Ads
- [ ] Landing pages relevant to each ad group; no broken links or 404s
- [ ] robots.txt allows AdsBot; sitemap.xml available
- [ ] Privacy policy, terms, cookie policy, disclaimer published (required by Google Ads policy)
- [ ] Business name, phone and location visible on every page
- [ ] Negative keyword list applied at account level
- [ ] Budget, bid strategy and ad schedule agreed with the owner

## First 8 weeks

Weekly: review the Search Terms report, add negatives (record them in `negative-keywords.md`), pause
keywords with clicks but no conversions, and check that the leads in the admin match the conversions
Google reports.
