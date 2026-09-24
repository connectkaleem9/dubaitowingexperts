# Design System

Owner: UX/UI agent · Updated 2026-09-22
Source of truth: the owner's homepage design in `docs/design/reference/`, implemented in
`public/assets/css/site.css` (`:root` tokens) and `public/assets/css/fonts.css`.

## Direction

Emergency-services red on deep navy, white cards on very light blue-grey sections, and a red/navy
hazard-chevron band as the signature divider. Colours were sampled directly from the supplied design.

## Colour tokens

| Token | Value | Use | Contrast |
|---|---|---|---|
| `--c-red` | `#DD0310` | Primary CTAs, accents, active nav, step numbers | White on it 5.1:1 ✓ |
| `--c-red-600` | `#B80009` | CTA hover | |
| `--c-red-50` | `#FFF1F2` | Icon tiles | |
| `--c-navy` | `#0B2946` | Dark bands ("Why choose us"), contact box | White 14:1 ✓ |
| `--c-navy-800` | `#04264A` | Top bar, mobile CTA bar | |
| `--c-navy-900` | `#011B30` | Footer | |
| `--c-blue-cta` | `#053B78` | "Stuck on the road?" card gradient | |
| `--c-wa` | `#0A7C26` | WhatsApp buttons | White 5.4:1 ✓ |
| `--c-heading` | `#0F2744` | Headings | 13.6:1 |
| `--c-text` | `#40536B` | Body text | 7.6:1 |
| `--c-muted` | `#64748B` | Secondary text | 4.8:1 |
| `--c-surface` | `#F7FBFE` | Services / form sections | |
| `--c-surface-2` | `#F1F7FA` | Areas section, breadcrumbs | |
| `--c-border` | `#E6EDF3` | Card and input borders | |

**Deviation from the design:** the design's WhatsApp green (`#0AAD32`) gives white text only 3.0:1,
below WCAG AA for button labels, so it is darkened to `#0A7C26`. Visually near-identical, legible for
everyone.

## Typography

**Poppins** (SIL OFL), self-hosted latin subset, weights 400/500/600/700/800 — 38 KB total, no
third-party request (decision D-011 replaces D-008's system-font-only rule). `poppins-700` is
preloaded; `font-display: swap` with a system fallback stack.

- H1 `clamp(2.1rem, 1.35rem + 3.1vw, 3.4rem)` / 800 / -0.02em
- H2 `clamp(1.6rem, 1.25rem + 1.6vw, 2.35rem)` / 800
- H3 `1.15rem` / 700 · Body `1rem` / 1.7 · Small `0.875rem`
- Section eyebrow: 0.8rem, 700, uppercase, `.14em` tracking, with a 34×4px red rule before it

## Layout & shape

Container `min(1200px, 100% - 2rem)`; radii 6/10/16px and pill; shadows `--sh-1` (cards) and
`--sh-2` (hover, dropdowns, CTA); spacing scale 4→96px; breakpoints 460, 560, 768, 900, 1024, 1100.

## Components

- **Top bar** — navy strip: 24/7, Dubai coverage, price-before-dispatch, and "Dubai – UAE" right.
- **Header** — white, sticky, logo (owner's tow-truck artwork), centred nav with dropdowns for
  Services and Areas, red phone button. The nav carries Home, Services, Areas, Projects, Reviews,
  About and Contact; FAQ and Blog live in the footer only (2026-09-23). Below 1100px it collapses to a hamburger; below 460px the
  phone button shows the icon only.
- **Hero** — sunset-to-blue gradient, skyline silhouette, red eyebrow rule, H1 with red "in Dubai",
  stacked Call / WhatsApp buttons with two-line labels, white "Request a Quote", a four-item stat row
  and the hazard-chevron band at the bottom. A photo (Settings → hero image) turns it into two columns.
- **Service card** — icon tile (or photo when one exists), title, one-line description, red "Learn More →".
- **Why-choose band** — navy with skyline, six icon + label items.
- **Steps** — red numbered circles, icon + title + description, with the red "Need immediate help?" card.
- **Area card** — photo (or light placeholder) with the area name beneath.
- **Review card** — initials avatar (never a stock face), name, stars, quote. On the home page the
  approved reviews sit in a **carousel** with previous/next arrows and dots (manual, wrapping,
  arrow-key accessible, one slide visible at a time); with no approved reviews it shows an invitation
  to leave one instead.
- **Project tile** — compact card used in the home-page projects slider: photo, title, and the area
  (or service) beneath.
- **FAQ** — white rows with a red +/− toggle, native `<details>` so it works without JavaScript.
- **CTA card** — blue gradient with skyline and the two-line Call / WhatsApp buttons.
- **Footer** — navy: brand blurb, Quick Links, Our Services (two columns), Contact Us with icons,
  social icons (only when links are configured), legal bar with the current year.
- **Sticky mobile bar** — Call + WhatsApp, hidden from 1100px up.

## Imagery (owner-supplied, 2026-09-23)

Source files live in `docs/design/reference/`; the optimised versions are generated into
`public/assets/img/` and the media library.

| Asset | Source | Output | Used by |
|---|---|---|---|
| Hero background | *Hero section backgroud image.png* | `hero-bg.webp` (1920px, 158 KB) | `.hero--photo` with a white overlay |
| Why-choose background | *Why Choose Us section background picture.png* | `why-bg.webp` (1920px, 38 KB) | `.section--photo` with a navy overlay |
| CTA card background | *Stuck on the Road section background image .png* | `cta-bg.webp` (1400px, 59 KB) | `.cta-card` with a blue overlay |
| Header logo (navy) | *Header logo.png* | `logo-header.webp` (trimmed, 26 KB) | header, landing pages |
| Footer logo (white) | *footer logo.png* | `logo-footer.webp` (trimmed, 31 KB) | footer |
| 5 service photos | *…services image.png* | media library (400/800/1600 WebP) | service cards + service page heroes |

Overlays are tuned so text keeps AA contrast over the photos at every width.

Still missing / deliberate:

- **Accident Recovery has no photo yet** — that card shows the icon tile until one is supplied.
- Area tiles are **name only, no images** (owner's instruction 2026-09-23).
- Project cards use a neutral placeholder until real job photos are added in the admin.
- Reviews use initials avatars — never stock portraits, which would misrepresent real customers.
- **Settings → Homepage hero photo** is an optional override that swaps the background for a
  picture beside the headline.

## Hero treatment

**Desktop (≥900px):** the photo is a background layer on the right that dissolves into the light
panel holding the copy, so headline, lead, buttons and the four stats always sit on clean
background. `background-position: 72%` keeps the billboard in the photo out of frame so the
"Anytime, anywhere" script line sits over open sky.

**Phones (<900px):** the photo is the section's **background image at full width and full strength**
(`background-size: 100% auto`, top centre), so the complete picture is visible with nothing cropped
or faded. The copy starts below it — the hero's `padding-top` equals the image height at 100% width
(41.667vw for the 1920×800 source).

Two CTAs only — Call Now and WhatsApp Now. ("Request a Quote" was removed on 2026-09-23; the quote
form is still reachable from the nav, the sticky bar and the form section lower down.) The hazard
chevron strip that used to close the hero was also removed.

## Sliders (marquees)

Three auto-sliders share the same mechanism (`.marquee` → `.marquee__track` → two
`.marquee__group`s, the second `aria-hidden` + `inert` for a seamless loop):

| Slider | Direction | Where it slides |
|---|---|---|
| `#services-marquee` | left → right (`marquee-right`) | all widths |
| `#projects-marquee` | left → right | all widths |
| `#hero-stats-marquee` | right → left (`marquee-left`) | phones only (`.marquee--mobile`) |
| `#usp-marquee` (why choose us) | right → left | phones only |

`.marquee--mobile` reverts to the normal grid from 900px up: animation off, duplicate group hidden,
overflow visible — so desktop layout is untouched. Each slider has its own **Pause/Play button**
(WCAG 2.2.2) and pauses on hover/focus.

## Services marquee

The services row auto-scrolls continuously left → right (`@keyframes marquee-right`, 46s linear,
infinite) and never shows a scrollbar. The track renders the cards twice; the duplicate set is
`aria-hidden` and `inert` so screen readers and keyboard users see each service once.

It pauses on hover and on focus, and there is an explicit **Pause/Play button** beside the section
heading — that is the WCAG 2.2.2 mechanism for content that moves on its own, and it works on touch
devices where hover does not exist. Under `prefers-reduced-motion: reduce` the marquee slows to 60s
instead of stopping; the blanket "no animation" rule at the end of the stylesheet deliberately
excludes `.marquee__track`, otherwise the slider would appear frozen for anyone who has reduced
motion enabled in their OS.

## Areas

Tiles are **pin icon + place name only** — no photography. Every area we cover is listed: those with
a written page link to it, the rest render as plain tiles, so the coverage list is complete without
creating thin pages or dead links.

## Accessibility notes

Focus ring: 3px `--c-red` with 2px offset. Touch targets ≥ 44px (buttons are 50–58px).
All text meets AA. Motion is limited to 120–150ms and disabled under `prefers-reduced-motion`.
`tools/shots.js` checks every breakpoint for horizontal overflow.
