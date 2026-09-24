# Google Ads Keywords & Structure

Owner: Google Ads agent · Updated: 2026-09-21 · Status: draft — validate volumes/CPCs in
Keyword Planner before launch.

Targeting: Location = Dubai (presence, not "interest"); Languages = English, Arabic; Devices =
all, bid-adjust mobile up; Schedule = match confirmed operating hours (unknown — ask owner).
Match types: phrase + exact to start; broad only with Smart Bidding after conversion data exists.

## Campaign 1 — Car Recovery Dubai
| Ad group | Keywords | Landing page |
|---|---|---|
| Car Recovery | "car recovery dubai", [car recovery dubai], "car recovery service dubai", "dubai car recovery", "vehicle recovery dubai", "recovery truck dubai" | /services/car-recovery/ (or /landing/car-recovery-dubai/ for A/B) |
| Emergency Car Recovery | "emergency car recovery dubai", "breakdown recovery dubai", "car breakdown recovery dubai" | /services/breakdown-recovery/ |
| Near Me | "car recovery near me", "recovery near me" | /landing/car-recovery-dubai/ |
| 24 Hour (paused until confirmed) | "24 hour car recovery dubai", "24/7 car recovery dubai" | /landing/car-recovery-dubai/ |

## Campaign 2 — Towing Dubai
| Ad group | Keywords | Landing page |
|---|---|---|
| Towing Service | "towing service dubai", "towing company dubai" | /services/towing-service/ |
| Car Towing | "car towing dubai", "tow truck dubai", "tow truck near me" | /services/towing-service/ |
| Emergency Towing | "emergency towing dubai", "accident towing dubai" | /landing/emergency-towing-dubai/ |

## Campaign 3 — Roadside Assistance Dubai
| Ad group | Keywords | Landing page |
|---|---|---|
| Roadside Assistance | "roadside assistance dubai", "roadside assistance service dubai" | /services/roadside-assistance/ |
| Breakdown Assistance | "car breakdown assistance dubai", "car breakdown dubai" | /landing/roadside-assistance-dubai/ |
| Flat Tyre | "flat tyre assistance dubai", "flat tyre service dubai", "flat tire help dubai" | /services/flat-tyre-assistance/ |
| Battery (if offered) | "battery jump start dubai", "car battery boost dubai" | /services/roadside-assistance/ |

## Campaign 4 — Brand
"dubai towing experts", [dubaitowingexperts] → /

## Ad copy rules
- Headlines may use: service name, "Dubai", "Call Now", "WhatsApp Us", "Quote Before Dispatch",
  area names. **Not allowed until confirmed:** 24/7, minutes-to-arrival, prices, licences,
  ratings, "cheapest", "No.1".
- Assets: call asset (052 585 1934), sitelinks (services, areas, reviews, contact), location
  asset once Google Business Profile is verified.

## Conversions (see docs/analytics-tracking.md)
Primary: `phone_click`, `whatsapp_click`, `form_submit` (lead), Google forwarding-number calls
≥ 60s (if call asset used). Secondary: `form_start`, `quote_request`.
