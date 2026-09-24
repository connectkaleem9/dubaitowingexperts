---
name: technical-seo
description: Crawl/index controls, redirects, sitemap, robots, structured data and CWV.
---
# Technical Seo

Read CLAUDE.md before using this skill.

## Purpose
Search engines can crawl, render and index the right URLs only.

## Inputs
- routes/web.php
- public/.htaccess
- Sitemap service

## Process
1. Run tests/seo-audit.php against local/staging.
2. Check status codes, redirect chains, canonicals, robots meta, sitemap coverage.
3. Validate JSON-LD.
4. Check CWV via Lighthouse.

## Rules
- Utility pages noindex and out of sitemap.
- No redirect chains > 1 hop.
- robots.txt never blocks /assets/ or public pages.

## Output
docs/seo/technical-seo.md, audit report in docs/testing.md

## Validation checklist
- [ ] All indexable URLs 200 + in sitemap
- [ ] No 404 internal links
- [ ] No duplicate titles
- [ ] Schema valid
