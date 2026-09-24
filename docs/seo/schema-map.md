# Structured Data Map

Owner: Schema/Data agent · Built by `app/Services/Schema.php` · Updated 2026-09-22

Every page outputs a single `<script type="application/ld+json">` containing an `@graph`.

| Page | Nodes |
|---|---|
| All pages | `AutomotiveBusiness` (@id `/#business`), `WebSite` (@id `/#website`), `WebPage` |
| All except home | `BreadcrumbList` (@id `<url>#breadcrumb`) |
| `/services/{slug}/` | `Service` (provider → business, areaServed = Dubai + linked areas) |
| `/faq/` | `WebPage` typed as `FAQPage` with `mainEntity` Question/Answer pairs |
| `/contact/` | `WebPage` typed as `ContactPage` |
| `/about/` | `WebPage` typed as `AboutPage` |
| `/blog/{slug}/` | `Article` (headline, dates, publisher, image, mainEntityOfPage) |

## Business node

```
@type      AutomotiveBusiness
name       Dubai Towing Experts
url        https://dubaitowingexperts.com/
telephone  +971525851934
address    PostalAddress { addressLocality: Dubai, addressRegion: Dubai, addressCountry: AE }
areaServed City "Dubai"
image/logo /assets/img/og-default.jpg, /assets/img/logo.svg
```

**Deliberately absent until the owner confirms them** (adding invented values would be a policy
violation and could mislead users):

- `streetAddress` — no public address has been provided
- `openingHours` — no confirmed hours; we never claim 24/7
- `geo` coordinates
- `priceRange`
- `sameAs` — added automatically once social/Google Business Profile links are saved in settings
- `aggregateRating` / `review` — Google does not allow self-serving review markup on a
  LocalBusiness's own site, so first-party reviews are shown to users but **not** marked up.
  If the business later collects reviews on Google, ratings surface through the Business Profile instead.

## Validation

1. `php tests/run.php` — checks the JSON parses and contains the expected node types, and that no
   address/hours/rating has crept in.
2. Before launch, run each page type through the
   [Rich Results Test](https://search.google.com/test/rich-results) and the
   [schema.org validator](https://validator.schema.org/).
3. After launch, watch Search Console → Enhancements for structured-data errors.
