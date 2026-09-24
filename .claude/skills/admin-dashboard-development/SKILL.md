---
name: admin-dashboard-development
description: Build admin CRUD modules.
---
# Admin Dashboard Development

Read CLAUDE.md before using this skill.

## Purpose
Let the owner manage content without code changes.

## Inputs
- Module spec (Master Plan sections 14, 43-45)

## Process
1. Routes under /admin behind AuthMiddleware.
2. List (filter, search, paginate) -> create/edit form -> delete via POST+CSRF.
3. Log each action in activity_logs.
4. Flash messages.

## Rules
- Every action authorised by role.
- IDs validated as int and existence-checked.
- No GET requests change state.

## Output
admin/Controllers/*, admin/views/*

## Validation checklist
- [ ] Unauthenticated access redirects to login
- [ ] CRUD round-trip works
- [ ] Activity logged
