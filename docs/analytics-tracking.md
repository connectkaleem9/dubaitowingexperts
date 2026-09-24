# Analytics & Conversion Tracking

Owner: Google Ads agent · Updated 2026-09-22 · Status: implemented in code, **not yet connected**
(no GTM container ID has been supplied — Admin → Site Settings → Google Tag Manager ID).

## How it is wired

- `resources/partials/analytics-head.php` loads **Google Tag Manager** only when a valid `GTM-…` ID
  is saved in settings, and initialises **Consent Mode v2** (`ad_storage`, `ad_user_data`,
  `ad_personalization`, `analytics_storage`) with the default from settings (`denied` by default).
- The cookie banner (`resources/partials/consent.php` + `app.js`) records the visitor's choice in
  `localStorage` and sends `gtag('consent','update', …)`. With `consent_default = granted` no banner
  is shown — only choose that if the owner accepts the compliance implications.
- `public/assets/js/app.js` pushes events to `dataLayer`. CTAs carry `data-track` and
  `data-location` attributes, so every event says *where* on the page it happened.

## Events pushed by the site

| Event | Fired when | Parameters |
|---|---|---|
| `phone_click` | Any `tel:` link/button is clicked | `link_location` (header, hero, sticky_bar, sidebar, footer, cta_band, form, thank_you, error_page, landing_hero…), `link_url`, `page_type` |
| `whatsapp_click` | Any WhatsApp link is clicked (including "Send my location") | same as above |
| `form_start` | First focus inside a tracked form | `form_name` (contact, quote, landing, review) |
| `form_submit` | Thank-you page after a contact form | `page_type` |
| `quote_request` | Thank-you page after a quote/landing form | `page_type` |
| `review_submit` | Review submitted successfully | — |
| `service_view` | A `/services/*` page loads | `page_path` |
| `area_view` | An `/areas/*` page loads | `page_path` |
| `project_view` | A `/projects/*` page loads | `page_path` |
| `page_view` | Automatic (GA4 via GTM) | `page_type` is in the data layer before GTM loads |

`page_type` values: `home`, `services`, `service`, `areas`, `area`, `projects`, `project`, `reviews`,
`faq`, `contact`, `about`, `blog`, `article`, `legal`, `landing`, `thank_you`, `page`.

## GTM setup (once the container exists)

1. **Variables** — Data Layer Variables: `page_type`, `link_location`, `link_url`, `form_name`.
2. **Triggers** — Custom Event triggers for each event name above.
3. **Tags**
   - GA4 Configuration tag (Measurement ID), fires on All Pages.
   - GA4 Event tags for `phone_click`, `whatsapp_click`, `form_start`, `form_submit`,
     `quote_request`, `review_submit`, passing `link_location` / `form_name`.
   - Google Ads Conversion tags for the primary conversions below (one conversion action each).
   - Google Ads Conversion Linker tag, All Pages.
4. **Consent settings**: set each tag's "Additional consent checks" to require
   `analytics_storage` (GA4) or `ad_storage` + `ad_user_data` (Ads).
5. Verify in **Preview** mode: click a call button, a WhatsApp button, and complete a test enquiry.

## Conversions

| Priority | Conversion | Source |
|---|---|---|
| Primary | Phone call click | `phone_click` |
| Primary | WhatsApp click | `whatsapp_click` |
| Primary | Lead form submission | `form_submit` |
| Primary | Quote/landing form submission | `quote_request` |
| Secondary | `form_start`, `review_submit`, `service_view`, `area_view` | engagement |

Notes:
- Count phone/WhatsApp clicks as **one per session** in Google Ads (people tap twice), and treat them
  as the main signal for Smart Bidding until enough form leads accumulate.
- Google Ads **call asset** conversions (calls ≥ 60 s via a forwarding number) are configured in the
  Ads account, not on the site.
- The site also stores the `gclid` and `utm_*` values with each lead (`contact_submissions`), so
  offline results can be matched to campaigns and uploaded as offline conversions later.

## Search Console

Verify the domain property, submit `/sitemap.xml`, and check Page Indexing after launch. Compare
"queries" against `docs/seo/keyword-map.md` monthly and fold new keywords into the map.
