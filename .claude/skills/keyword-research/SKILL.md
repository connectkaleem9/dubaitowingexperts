---
name: keyword-research
description: Expand seed keywords into a Dubai recovery keyword universe with intent and priority.
---
# Keyword Research

Read CLAUDE.md before using this skill.

## Purpose
Build a complete, honest keyword database for Dubai vehicle recovery.

## Inputs
- Master Plan section 6 seeds
- docs/research/*
- Any Keyword Planner / Search Console exports in docs/research/data/

## Process
1. Start from seeds; expand with modifiers (emergency, 24 hour, near me, cheap, company, service, number).
2. Add service x area combinations for areas in docs/seo/local-seo.md.
3. Add informational questions (breakdown, accident, flat tyre, battery).
4. Tag each keyword: intent, funnel stage, relative priority, Ads suitability.
5. Record data source for every metric.

## Rules
- Never invent volume/CPC; use High/Med/Low + reasoning when no data.
- Keep UK/UAE spelling variants (tyre/tire).
- Exclude intents the business can't serve.

## Output
docs/seo/keyword-research.md

## Validation checklist
- [ ] Every keyword has intent + priority + source
- [ ] No fabricated numbers
- [ ] Service x area combos only for served areas
