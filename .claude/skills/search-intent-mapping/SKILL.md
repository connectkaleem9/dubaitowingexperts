---
name: search-intent-mapping
description: Map keyword clusters to page types and URLs.
---
# Search Intent Mapping

Read CLAUDE.md before using this skill.

## Purpose
Ensure each search intent has exactly one best page.

## Inputs
- Clusters
- Site architecture (Master Plan section 8)

## Process
1. For each cluster choose page type: home, service, area, landing, blog, FAQ, contact.
2. Assign URL.
3. Note conversion goal per page.
4. Detect cannibalisation (two URLs, one intent).

## Rules
- Informational -> blog/FAQ, never a service page.
- Emergency/transactional -> service or landing page with call above the fold.

## Output
docs/seo/keyword-map.md (keyword -> intent -> cluster -> page type -> URL)

## Validation checklist
- [ ] No orphan clusters
- [ ] No duplicate URL targets
- [ ] Every URL has a conversion goal
