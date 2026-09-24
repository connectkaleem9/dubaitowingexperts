---
name: qa-testing
description: Run the project test suites and checklists.
---
# Qa Testing

Read CLAUDE.md before using this skill.

## Purpose
Prove features work before they are called done.

## Inputs
- Changed feature
- tests/
- Master Plan sections 48-50, 56

## Process
1. php tests/run.php.
2. php tests/seo-audit.php http://localhost:8000.
3. Manual: forms, admin CRUD, mobile widths.
4. Record results.

## Rules
- A failing check blocks 'done'.
- Log defects in PROJECT_STATUS.md.

## Output
docs/testing.md entry

## Validation checklist
- [ ] All suites green
- [ ] No placeholders on published pages
- [ ] Pre-launch checklist ticked
