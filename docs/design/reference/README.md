# Design reference and source artwork

The owner's design and original artwork. These files are **sources only** — they are never served
to visitors. Optimised copies live in `public/assets/img/` and in the media library.

## Current files

| File | Used as |
|---|---|
| `dubairecoveryexperts homepage.png` | The approved homepage design (layout, colours, sections) |
| `Hero section backgroud image.png` | Homepage hero background → `public/assets/img/hero-bg.webp` |
| `Why Choose Us section background picture.png` | "Why choose us" band → `why-bg.webp` |
| `Stuck on the Road section background image .png` | Bottom CTA card → `cta-bg.webp` |
| `Header logo.png` | Header logo (navy) → `logo-header.webp` |
| `footer logo.png` | Footer logo (white) → `logo-footer.webp` |
| `Car recovery services image .png` etc. | Service photos → media library, linked to each service |

## Re-generating the optimised assets

Backgrounds and logos are produced by a small GD script (resize → WebP; logos are alpha-trimmed).
Service photos go through the normal upload pipeline so they get 400/800/1600 WebP variants and an
alt text. If artwork is replaced, re-run the import or simply upload the new image in
Admin → Media and attach it to the service.

## Before implementing anything new from a design

1. Does it keep the call + WhatsApp CTA reachable on a phone (CLAUDE.md §10)?
2. Does it state claims we cannot evidence (prices, arrival times, ratings, licences, fleet size)?
   Those stay out whatever the mock-up shows — see CLAUDE.md §2 and decision D-012.
3. Does it need photography we do not have? List it in PROJECT_STATUS.md.
4. Colour/type changes belong in `docs/design/design-system.md` as tokens, not hard-coded in views.
