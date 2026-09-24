---
name: css-development
description: Write maintainable, token-based, mobile-first CSS.
---
# Css Development

Read CLAUDE.md before using this skill.

## Purpose
Consistent, fast, responsive styling.

## Inputs
- docs/design/design-system.md

## Process
1. Use custom properties from :root.
2. Mobile-first min-width queries.
3. Component classes (BEM-like: .card, .card__title).

## Rules
- No hardcoded colours outside tokens.
- No !important except utilities.
- Respect prefers-reduced-motion.

## Output
public/assets/css/site.css, admin.css

## Validation checklist
- [ ] No horizontal scroll at 320px
- [ ] Focus styles visible
- [ ] Contrast AA
