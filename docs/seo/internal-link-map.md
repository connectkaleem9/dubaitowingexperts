# Internal Link Map

Owner: SEO Strategy agent · Updated 2026-09-22

```
Home ──► Services hub ──► Service page ──┬──► Related services (3–4)
  │            │                         ├──► Area pages that list the service
  │            │                         ├──► Projects for that service
  │            │                         └──► Service FAQs
  ├──► Areas hub ──► Area page ──────────┬──► Services available in that area
  │                                      ├──► Projects in that area
  │                                      └──► Other areas (max 6)
  ├──► Projects ──► Project page ────────┬──► Its service page
  │                                      ├──► Its area page
  │                                      └──► More recent jobs
  ├──► Reviews ──► review form
  ├──► FAQ ──► service/area pages where relevant
  ├──► Blog ──► guide ──► the service the guide is about
  └──► Contact / About / legal (footer)
```

## Rules

- Every published service and area page is reachable from the header menu, the footer, and at least
  one contextual in-content link — no orphans (the crawler in `tests/seo-audit.php` walks every link).
- Anchor text is varied and descriptive: "More about car recovery", "Recovery in Dubai Marina",
  "what to do when your car breaks down in Dubai". Never the same exact-match anchor repeatedly.
- Service pages link to areas as a chip list (navigation), not as headings, so area pages own the
  `{service} {area}` intent.
- Blog guides link **down** to the relevant service page; service pages link **out** to the guide once,
  in a "read more" position.
- `/landing/*` pages deliberately link out only to legal pages and the main site — they stay
  distraction-free, and are `noindex` so they do not compete for organic traffic.
- Footer links are sitewide and low-weight; they exist for users and crawl discovery, not for anchor juice.

## Adding a new page

1. Check `keyword-map.md` — does an existing page already target the intent? If yes, add a section there instead.
2. Link it from: the relevant hub, at least one sibling page, and one contextual paragraph.
3. Re-run `php tests/seo-audit.php <url>` to confirm there are no orphans or broken links.
