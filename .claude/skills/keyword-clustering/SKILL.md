---
name: keyword-clustering
description: Group keywords into clusters that each deserve one URL.
---
# Keyword Clustering

Read CLAUDE.md before using this skill.

## Purpose
Turn the keyword universe into non-overlapping clusters.

## Inputs
- docs/seo/keyword-research.md

## Process
1. Group by identical SERP intent (same result type would satisfy both).
2. Name each cluster by its head term.
3. Pick primary + secondaries.
4. Flag clusters too small for their own page -> merge into parent.

## Rules
- One cluster = one page.
- Don't split near-synonyms (car recovery / vehicle recovery) unless SERPs differ.

## Output
Cluster table in docs/seo/keyword-map.md

## Validation checklist
- [ ] No keyword in two clusters
- [ ] Every cluster has a primary keyword
- [ ] Merge decisions explained
