---
name: mysql-development
description: Design and change the MySQL schema safely.
---
# Mysql Development

Read CLAUDE.md before using this skill.

## Purpose
Correct, indexed, constraint-backed data model.

## Inputs
- Feature data needs
- docs/database.md

## Process
1. Write new numbered migration.
2. Add FKs + indexes.
3. Update docs/database.md.
4. Run migrate.php on dev.

## Rules
- Never edit applied migrations.
- utf8mb4 + InnoDB.
- Explicit ON DELETE.

## Output
database/migrations/NNN_*.sql

## Validation checklist
- [ ] Migration runs clean on empty DB
- [ ] EXPLAIN on listing queries uses indexes
