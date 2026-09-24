---
name: google-ads-landing-page
description: Build or optimise a paid-traffic landing page for one ad group.
---
# Google Ads Landing Page

Read CLAUDE.md before using this skill.

## Purpose
Maximise Quality Score and conversion rate for a specific ad group.

## Inputs
- docs/seo/google-ads.md ad group
- Keyword list
- Ad copy

## Process
1. Mirror ad headline in H1.
2. Call + WhatsApp above the fold on 360px screen.
3. Short form (name, phone, location, service).
4. Service facts, process, trust (real reviews only), FAQ.
5. Minimal nav; no outbound distractions.

## Rules
- /landing/* pages are noindex,follow.
- No unconfirmed claims.
- LCP < 2.5s mobile.

## Output
resources/views/landing/<slug>.php + route + seo_metadata

## Validation checklist
- [ ] H1 matches ad intent
- [ ] CTAs tracked
- [ ] Form -> thank-you with conversion event
- [ ] Mobile Lighthouse >= 90 performance
