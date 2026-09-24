---
name: on-page-seo
description: Write and verify title, meta, headings, canonical, OG and on-page keyword use.
---
# On Page Seo

Read CLAUDE.md before using this skill.

## Purpose
Every indexable page is correctly optimised for its mapped intent.

## Inputs
- docs/seo/keyword-map.md
- Page content

## Process
1. Title: primary keyword + differentiator + brand (<=60).
2. Meta: benefit + CTA (<=155).
3. H1 matches intent; H2s cover subtopics.
4. Canonical absolute, self-referencing.
5. OG/Twitter set.
6. Internal links per link map.

## Rules
- Unique title/meta site-wide.
- One H1.
- No stuffing: primary keyword appears naturally, not forced.

## Output
docs/seo/on-page-seo.md + seo_metadata rows

## Validation checklist
- [ ] Title/meta lengths OK and unique
- [ ] Exactly one H1
- [ ] Canonical correct
- [ ] Alt text on all content images
