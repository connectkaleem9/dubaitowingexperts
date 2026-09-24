# Deployment

Updated 2026-09-24 · Target: typical UAE shared/cPanel hosting with Apache, PHP 8.1+, MySQL 8 / MariaDB 10.6+

## Requirements

PHP extensions: `pdo_mysql`, `mbstring`, `gd` (with WebP), `fileinfo`, `dom`, `json`, `exif` (optional).
Apache modules: `mod_rewrite`, ideally `mod_headers` and `mod_deflate`.

## The live site

Deployed 2026-09-24. Hostinger shared hosting, PHP 8.3.33, MariaDB 11.8.9.

| | |
|---|---|
| URL | https://dubaitowingexperts.com |
| Project root | `~/dubaitowingexperts` (outside the web root) |
| Document root | `~/domains/dubaitowingexperts.com/public_html` → symlink to `~/dubaitowingexperts/public` |
| SSH | port 65002, key `~/.ssh/dte_deploy` |
| Backups | every deploy writes `~/dte-backups/code-<stamp>.tar.gz` and `db-<stamp>.sql` |

Two host quirks are worth knowing:

- **LiteSpeed overwrites the CSP header.** The policy is therefore served as a `<meta>` tag and
  `.htaccess` carries only `frame-ancestors` (decision D-014). If the CSP ever looks wrong, check
  `App\Middleware\SecurityHeaders::cspDirectives()` — it feeds both.
- **`shell_exec` is disabled**, so CLI scripts must not depend on it.
- **The CDN challenges headless browsers** with a "Checking your browser" page. Real browsers and
  Googlebot are unaffected; point screenshot tooling at a local copy.

### Moving content between environments

`php tools/export-content.php` writes `deploy/content.sql` (media, services, areas, projects, FAQs,
posts, SEO overrides, settings). Copy it up and `mysql -u<user> -p <db> < content.sql`, then copy
`public/uploads/` across so the media files match the rows. Accounts, logs, leads and **reviews** are
never exported — reviews must only ever be real ones entered in production.

## Deploying over SSH (scripted)

`tools/deploy.ps1` builds a clean release, uploads it as one archive, extracts it on the server,
keeps `.env` and `public/uploads` untouched, fixes permissions and runs migrations. It backs up the
current code **and** the database to `../dte-backups/` before every deploy.

```powershell
# once: create the production .env locally (asks for the DB password, generates APP_KEY)
powershell -ExecutionPolicy Bypass -File tools\make-env.ps1 `
    -Url https://dubaitowingexperts.com -DbName <db> -DbUser <user> -NotifyEmail <owner email>

# first deploy (uploads the .env too)
powershell -ExecutionPolicy Bypass -File tools\deploy.ps1 `
    -Server <ip> -User <ssh user> -Port <port> -RemotePath /home/<user>/dubaitowingexperts `
    -KeyFile $HOME\.ssh\dte_deploy -EnvFile deploy\.env.production `
    -Url https://dubaitowingexperts.com

# later deploys
powershell -ExecutionPolicy Bypass -File tools\deploy.ps1 `
    -Server <ip> -User <ssh user> -Port <port> -RemotePath /home/<user>/dubaitowingexperts `
    -KeyFile $HOME\.ssh\dte_deploy -Url https://dubaitowingexperts.com
```

Useful switches: `-DryRun` (build only, never touches the server), `-SkipMigrate`,
`-Php /usr/local/bin/php8.2` when the host's PHP is not simply `php`.

What ships: `app/ admin/ config/ database/ public/ resources/ routes/ storage/ .htaccess .env.example`.
What never ships: `.env`, `docs/`, `tools/`, `tests/`, `.claude/`, markdown, the original brief,
logs, caches and uploaded media.

### Server prerequisites

1. **Document root** must point at `<RemotePath>/public`. If the host forces `public_html`, either
   set `RemotePath` to the parent of `public_html` and symlink, or upload into `public_html` and rely
   on the root `.htaccess` (less safe — see below).
2. **SSH key**: add the deploy public key to `~/.ssh/authorized_keys` on the server so the script can
   run without a password prompt.
3. **PHP 8.1+ CLI** available for migrations, with `pdo_mysql`, `mbstring`, `gd` (WebP), `fileinfo`, `dom`.
4. **MySQL/MariaDB** database and user created; credentials go in the `.env`.

### Rollback

Each deploy leaves `../dte-backups/code-<timestamp>.tar.gz` and `../dte-backups/db-<timestamp>.sql`.
To roll back: extract the code archive over the project directory and, if needed,
`mysql -u<user> -p <db> < ../dte-backups/db-<timestamp>.sql`.

## First deployment

1. **Create the database** and a dedicated MySQL user with rights on that database only.
2. **Upload the project** above the web root (e.g. `/home/site/dre/`), excluding `.env`,
   `storage/logs/*`, `storage/cache/*`, `public/uploads/*` and `tests/`.
3. **Point the document root at `public/`**. If the host cannot change it, upload the whole project
   inside `public_html/` — the root `.htaccess` will route requests into `public/` and block source
   directories, but changing the document root is safer.
4. **Create `.env`** from `.env.example` in the project root (not in `public/`):
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://dubaitowingexperts.com
   APP_KEY=<php -r "echo bin2hex(random_bytes(32));">
   DB_HOST=localhost
   DB_DATABASE=…
   DB_USERNAME=…
   DB_PASSWORD=…
   MAIL_TO=<owner email>
   FORCE_NOINDEX=false
   ```
5. **Permissions**: `storage/logs`, `storage/cache` and `public/uploads` writable by PHP (755/775);
   everything else read-only. Confirm `public/uploads/.htaccess` uploaded.
6. **Run migrations and seed**: `php database/migrate.php --seed`
7. **Create the first admin**: `php database/create-admin.php "Owner Name" owner@example.com owner`
   (if the host has no CLI, run it once through a temporary protected script, then delete it).
8. **Check HTTPS** and that `http://` + `www.` redirect once to `https://dubaitowingexperts.com`.
9. **Smoke test**: home, a service page, an area page, `/contact/` (submit a real test enquiry),
   `/reviews/` (submit a test review, then delete it), `/sitemap.xml`, `/robots.txt`, `/admin/login/`.
10. **Run the audits** from a machine with PHP:
    `php tests/seo-audit.php https://dubaitowingexperts.com`
11. **Search Console**: verify the property, submit `https://dubaitowingexperts.com/sitemap.xml`.
12. **Analytics**: create the GTM container, put its ID in Admin → Site Settings, configure the tags
    in `docs/analytics-tracking.md`, then test with GTM Preview.

## Routine updates

1. Back up: `mysqldump --single-transaction dubairecovery > backup-$(date +%F).sql` and copy `public/uploads/`.
2. Upload changed files (never overwrite `.env` or `public/uploads/`).
3. Run `php database/migrate.php` if `database/migrations/` changed.
4. Smoke test the pages you touched; run `php tests/seo-audit.php <url>`.
5. Record the release in `CHANGELOG.md`.

## Staging

Use a separate database and `.env` with `APP_ENV=staging` and `FORCE_NOINDEX=true` — every response
then carries `X-Robots-Tag: noindex`, so staging can never be indexed. Protect it with HTTP auth too.

## Nginx equivalent (if the host uses Nginx)

```nginx
root /home/site/dre/public;
index index.php;
location / { try_files $uri /index.php$is_args$args; }
location ~ ^/uploads/.*\.(php|phtml|phar)$ { deny all; }
location ~* \.(css|js|svg|webp|png|jpg|woff2)$ { expires 1y; add_header Cache-Control "public, immutable"; }
location ~ \.php$ { include fastcgi_params; fastcgi_pass unix:/run/php/php8.3-fpm.sock; fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; }
```
Add the HTTPS/non-www redirect in the server block, and keep `/admin` reachable (no IP allow-list unless
the owner has a static IP).

## Local development

```
php -S 127.0.0.1:8080 -t public public/router-dev.php
php database/migrate.php --seed
php tests/run.php http://127.0.0.1:8080
php tests/seo-audit.php http://127.0.0.1:8080
php tests/admin-e2e.php http://127.0.0.1:8080 <admin-email> <password>
```
