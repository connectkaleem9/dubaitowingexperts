---
name: schema-implementation
description: Generate valid JSON-LD for a page type.
---
# Schema Implementation

Read CLAUDE.md before using this skill.

## Purpose
Structured data that is accurate and eligible.

## Inputs
- Page data
- docs/seo/schema-map.md

## Process
1. Use App\Services\Schema builders.
2. Output one @graph per page.
3. Validate in Rich Results Test.

## Rules
- Only visible content.
- No self-serving LocalBusiness review stars.
- No invented address/hours/rating.

## Output
JSON-LD in page head

## Validation checklist
- [ ] Parses as JSON
- [ ] Types match schema-map
- [ ] No warnings for required fields
