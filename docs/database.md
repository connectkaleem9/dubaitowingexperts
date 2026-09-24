# Database

MySQL 8.0+ / MariaDB 10.6+ · InnoDB · utf8mb4_unicode_ci · Updated 2026-09-22

Schema changes go in a **new** numbered file in `database/migrations/`; never edit an applied one.

```
php database/migrate.php            # apply pending migrations
php database/migrate.php --status   # list applied / pending
php database/migrate.php --seed     # apply, then seed empty tables
php database/create-admin.php "Name" email@example.com owner
```

## Tables

| Table | Purpose | Notes |
|---|---|---|
| `admins` | Admin accounts | Argon2id/bcrypt hash, role `owner`/`editor`, lockout counters |
| `services` | Service pages | `slug` unique, `is_published`, sanitised HTML `body` |
| `areas` | Area pages | Same shape as services; unpublished until content passes the thin-page check |
| `service_area` | Which services show on which area page | Composite PK, both FKs cascade |
| `projects` | Real recovery jobs | FKs to service/area/media; `status` draft/published |
| `project_images` | Project gallery | Unique (project, media); cascade |
| `reviews` | Customer reviews | `status` pending/approved/rejected/hidden, `consent_at`, `ip_hash` |
| `review_images` | Photos attached to reviews | Cascade |
| `contact_submissions` | Leads from every form | Status pipeline, UTM/GCLID attribution, `ip_hash` (never the raw IP) |
| `faqs` | Questions | Optional link to a service or area; `show_on_home` |
| `posts` | Blog guides | `status`, `published_at` (future dates stay hidden) |
| `media` | Uploaded images | `path` without size suffix, `widths` list, `alt_text` |
| `seo_metadata` | Per-path title/description/robots/OG overrides | `path` unique |
| `settings` | Editable site settings | Key/value; `business.*` keys override `config/business.php` |
| `activity_logs` | Who changed what | Admin id, action, entity, IP |
| `rate_limits` | Fixed-window counters | Used by forms and login throttling |
| `migrations` | Applied migrations | Created by `migrate.php` |

## Conventions

- `id INT UNSIGNED AUTO_INCREMENT` primary keys; `created_at` / `updated_at` on content tables.
- Every foreign key has an explicit `ON DELETE` (`CASCADE` for child rows, `SET NULL` for optional links)
  and its own index.
- Listing indexes match the queries: `(is_published, sort_order)`, `(status, project_date)`,
  `(status, is_featured, approved_at)`, `(status, created_at)`.
- `CHECK (rating BETWEEN 1 AND 5)` on reviews.
- Personal data: IP addresses are stored only as an HMAC (`ip_hash`); uploaded images are re-encoded,
  which strips EXIF GPS data.

## Backups

Before any deployment: `mysqldump --single-transaction --routines dubairecovery > backup.sql`,
plus a copy of `public/uploads/`. See `docs/deployment.md`.
