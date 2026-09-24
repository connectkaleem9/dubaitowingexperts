---
name: deployment
description: Deploy to production hosting safely.
---
# Deployment

Read CLAUDE.md before using this skill.

## Purpose
Reliable, secure release.

## Inputs
- docs/deployment.md
- Hosting credentials (owner-provided, never committed)

## Process
1. Backup DB + files.
2. Upload code (excluding .env, storage contents, tests).
3. Set .env for production.
4. Run migrations.
5. Smoke test key URLs, forms, admin login.
6. Check robots/sitemap/HTTPS.

## Rules
- APP_DEBUG=false in production.
- Document root = public/.
- Irreversible steps require owner approval.

## Output
Release notes in CHANGELOG.md

## Validation checklist
- [ ] HTTPS + HSTS
- [ ] No debug output
- [ ] Forms deliver
- [ ] Sitemap submitted
