---
name: accessibility
description: Audit and fix accessibility to WCAG 2.2 AA.
---
# Accessibility

Read CLAUDE.md before using this skill.

## Purpose
Usable by keyboard, screen reader and low-vision users.

## Inputs
- Page or component

## Process
1. Keyboard walk-through.
2. Check labels, names, roles.
3. Contrast check.
4. Screen-reader spot check (NVDA/VoiceOver).

## Rules
- ARIA only when native HTML can't.
- Never remove focus outlines without replacement.

## Output
Fixes + notes in docs/testing.md

## Validation checklist
- [ ] Skip link works
- [ ] Menu operable by keyboard + Esc
- [ ] Form errors announced
- [ ] Contrast >= 4.5:1
