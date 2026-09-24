---
name: html-development
description: Write semantic, accessible, SEO-correct HTML templates.
---
# Html Development

Read CLAUDE.md before using this skill.

## Purpose
Clean markup that search engines and assistive tech understand.

## Inputs
- Wireframe / content
- Partials

## Process
1. Use landmarks (header, nav, main, footer).
2. Heading order without skips.
3. Reuse partials.
4. Escape output with e().

## Rules
- No div soup for buttons/links.
- No inline styles or handlers.

## Output
resources/views/*, resources/partials/*

## Validation checklist
- [ ] Validates (W3C) without errors
- [ ] One H1
- [ ] Landmarks present
- [ ] All images have width/height/alt
