# Keyword Map (keyword → intent → cluster → page → URL)

Owner: SEO Strategy agent · Updated: 2026-09-21
One cluster per URL. If a new page is proposed, check this table for cannibalisation first.

| URL | Page type | Cluster | Primary keyword | Secondary keywords | Conversion goal |
|---|---|---|---|---|---|
| / | Home | Brand + recovery service Dubai | recovery service dubai | dubai towing experts, recovery company dubai, car recovery near me | Call / WhatsApp |
| /services/ | Hub | Services overview | car recovery and towing services dubai | recovery services | Navigate → service |
| /services/car-recovery/ | Service | Car recovery | car recovery dubai | car recovery service dubai, vehicle recovery dubai, dubai car recovery, recovery truck dubai, flatbed recovery dubai | Call / WhatsApp |
| /services/towing-service/ | Service | Towing | towing service dubai | car towing dubai, tow truck dubai, towing company dubai, vehicle towing dubai | Call / WhatsApp |
| /services/roadside-assistance/ | Service | Roadside assistance | roadside assistance dubai | car breakdown assistance, battery jump start dubai*, fuel delivery*, lockout* | Call / WhatsApp |
| /services/breakdown-recovery/ | Service | Breakdown / emergency | breakdown recovery dubai | car breakdown recovery dubai, emergency car recovery dubai, car breakdown dubai | Call |
| /services/accident-recovery/ | Service | Accident | accident recovery dubai | accident car towing dubai, accident vehicle recovery | Call / WhatsApp |
| /services/flat-tyre-assistance/ | Service | Flat tyre | flat tyre assistance dubai | flat tyre service dubai, flat tire help dubai, tyre change roadside | Call / WhatsApp |
| /areas/ | Hub | Areas served | car recovery areas dubai | — | Navigate → area |
| /areas/{slug}/ | Area | car recovery {area} | car recovery {area} | towing {area}, recovery {area}, tow truck {area} | Call / WhatsApp |
| /projects/ | Listing | Proof | car recovery projects dubai | — | Trust → contact |
| /projects/{slug}/ | Project | Proof | (project-specific) | — | Trust → contact |
| /reviews/ | Listing | Reviews | dubai towing experts reviews | — | Trust / review submit |
| /faq/ | FAQ | Questions | car recovery dubai faq | how much does car recovery cost dubai | Call |
| /blog/ + /blog/{slug}/ | Articles | Informational | see keyword-research.md §9 | — | Soft CTA |
| /about/ | About | Brand | about dubai towing experts | — | Trust |
| /contact/ | Contact | Contact | contact car recovery dubai | car recovery number dubai | Form / Call |
| /landing/car-recovery-dubai/ | Ads (noindex) | Ads: car recovery | — | — | Call / form |
| /landing/emergency-towing-dubai/ | Ads (noindex) | Ads: emergency towing | — | — | Call |
| /landing/roadside-assistance-dubai/ | Ads (noindex) | Ads: roadside | — | — | Call / WhatsApp |

\* only once the owner confirms the sub-service.

Redirects: `/services/vehicle-recovery/` → 301 `/services/car-recovery/` (decision D-003).

## Cannibalisation guards
- "24/7" is confirmed (2026-09-22) and used on the home page title/H1 and the contact page title.
  Service and area pages stay free of it in titles so they do not all compete for the same modifier.
- Homepage targets the broad brand + "recovery service dubai"; car-recovery page owns
  "car recovery dubai". Homepage links to car-recovery with a descriptive anchor to reinforce this.
- Area pages own `{service} {area}`; service pages mention areas only as links, not headings.
