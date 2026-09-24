# Conversion Tracking

Owner: Google Ads agent · Updated 2026-09-22

The full event list, GTM setup and consent behaviour are documented in
[`../analytics-tracking.md`](../analytics-tracking.md). This page is the short reference for the
conversion actions themselves.

| Conversion action (Google Ads) | Source event | Count | Value | Notes |
|---|---|---|---|---|
| Phone click (website) | `phone_click` | One per session | Assign an estimated lead value once known | Main signal on mobile |
| WhatsApp click | `whatsapp_click` | One per session | same | Opens WhatsApp; the conversation happens off-site |
| Lead form | `form_submit` | Every | same | `/thank-you/` after `/contact/` |
| Quote request | `quote_request` | Every | same | Quote/landing forms |
| Calls from ads | Google forwarding number ≥ 60 s | Every | same | Configured in Google Ads, not on the site |

Secondary (import as "secondary" actions so they do not drive bidding): `form_start`,
`review_submit`, `service_view`, `area_view`, `project_view`.

## Attribution stored with each lead

`contact_submissions` keeps `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `gclid`,
`source_path` and `form_type` for every enquiry. That allows:

- checking Google's reported conversions against real enquiries in the admin,
- reporting which campaigns produce jobs that actually complete (lead status pipeline),
- uploading **offline conversions** later (GCLID + the time the job was completed), which is the most
  accurate signal for Smart Bidding in a phone-led business.

## Verification routine

1. GTM Preview: load the site, click a call button, a WhatsApp button, submit a test enquiry.
2. Check the events appear in GA4 DebugView and in Google Ads conversion diagnostics.
3. Confirm the test enquiry is in Admin → Contact Requests with the campaign fields populated
   (visit the site with `?utm_source=test&gclid=TEST123` first).
4. Delete the test enquiry afterwards.
