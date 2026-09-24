---
name: performance-optimization
description: Hit Core Web Vitals targets.
---
# Performance Optimization

Read CLAUDE.md before using this skill.

## Purpose
Fast pages, especially on mobile.

## Inputs
- Lighthouse / PageSpeed report

## Process
1. Measure first.
2. Fix LCP (hero image priority, server time), CLS (dimensions), INP (JS size).
3. Enable compression + caching headers.
4. Re-measure.

## Rules
- No render-blocking third-party scripts.
- Load GTM after interaction or async.

## Output
Changes + before/after numbers in docs/testing.md

## Validation checklist
- [ ] LCP < 2.5s
- [ ] CLS < 0.1
- [ ] INP < 200ms
- [ ] JS < 50KB compressed on public pages
