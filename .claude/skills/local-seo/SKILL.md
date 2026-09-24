---
name: local-seo
description: Keep NAP, LocalBusiness schema, area pages and GBP alignment correct.
---
# Local Seo

Read CLAUDE.md before using this skill.

## Purpose
Maximise local relevance for Dubai without thin or false content.

## Inputs
- config/business.php
- docs/research/market-research.md
- Owner-confirmed service areas

## Process
1. Verify NAP on header, footer, contact, schema, landing pages.
2. Write area briefs with genuinely local detail.
3. Link area <-> service pages.
4. Prepare GBP category/services/description checklist.

## Rules
- No invented address or hours.
- Area page only if served + unique content exists.

## Output
docs/seo/local-seo.md, area briefs

## Validation checklist
- [ ] NAP identical everywhere
- [ ] Schema has no invented fields
- [ ] Each area page passes uniqueness check (>=60% unique body text)
