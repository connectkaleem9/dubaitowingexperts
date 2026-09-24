# Dubai Towing Experts — website

Lead-generation website for a Dubai vehicle recovery business: car recovery, towing and roadside
assistance, built for local SEO and Google Ads, with an admin dashboard so the owner can manage
projects, reviews, leads, services, areas, FAQs, blog posts, media and SEO without a developer.

**Stack:** PHP 8.1+ (custom MVC, no framework or Composer), MySQL/MariaDB, semantic HTML5, hand-written
CSS and vanilla JS. Document root is `public/`.

## Quick start (local)

```bash
cp .env.example .env                 # then set DB_* and a random APP_KEY
php database/migrate.php --seed      # schema + starter content
php database/create-admin.php "Your Name" you@example.com owner
php -S 127.0.0.1:8080 -t public public/router-dev.php
```

Site: <http://127.0.0.1:8080/> · Admin: <http://127.0.0.1:8080/admin/login/>

On Windows you can use the helper script instead of the last command:

```powershell
powershell -ExecutionPolicy Bypass -File tools\serve.ps1            # start
powershell -ExecutionPolicy Bypass -File tools\serve.ps1 -Stop      # stop
powershell -ExecutionPolicy Bypass -File tools\serve.ps1 -PhpPath C:\php\php.exe
```

It finds `php` on PATH, or a portable build placed in `tools\php\`.

## Tests

```bash
php tests/run.php http://127.0.0.1:8080                    # unit + functional + security
php tests/seo-audit.php http://127.0.0.1:8080              # crawl + on-page SEO audit
php tests/admin-e2e.php http://127.0.0.1:8080 you@example.com 'password'

# responsive check: screenshots every width and fails on horizontal overflow (needs Chrome + Node)
node tools/shots.js http://127.0.0.1:8080 / /services/car-recovery/ --widths=320,390,768,1024,1440
```

## Layout

```
app/        core (router, request, view, database, auth), controllers, models, services, validation
admin/      admin module: controllers + views
config/     app, database, business (NAP), landing pages, redirects
database/   migrations, seeds, migrate.php, seed.php, create-admin.php
public/     index.php front controller, .htaccess, robots.txt, assets/, uploads/
resources/  public views and reusable partials
storage/    logs, cache
docs/       research, SEO, design, architecture, database, security, deployment, testing
tests/      run.php, seo-audit.php, admin-e2e.php
```

## Documentation

| Read this | For |
|---|---|
| [`CLAUDE.md`](CLAUDE.md) | Project rules: coding, SEO, security, content and design standards |
| [`PROJECT_STATUS.md`](PROJECT_STATUS.md) | What is done, what is next, and open questions for the owner |
| [`docs/architecture.md`](docs/architecture.md) | How a request flows through the code |
| [`docs/database.md`](docs/database.md) | Schema and migration workflow |
| [`docs/security.md`](docs/security.md) | Security controls and the pre-launch checklist |
| [`docs/deployment.md`](docs/deployment.md) | Going live, updates, staging |
| [`docs/testing.md`](docs/testing.md) | Test suites and latest results |
| [`docs/seo/`](docs/seo/) | Keyword research and map, on-page, technical, local SEO, Google Ads |
| [`docs/research/`](docs/research/) | Market and competitor research |
| [`docs/design/design-system.md`](docs/design/design-system.md) | Colours, type, spacing, components |

## Ground rules

Never publish claims the business has not confirmed — 24/7 availability, arrival times, prices,
licences, certifications, years of experience, fleet size, ratings. Never create fake reviews or
projects. See `CLAUDE.md` §2 and §11.
