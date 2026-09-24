<#
.SYNOPSIS
    Deploys the Dubai Towing Experts website to a Linux server over SSH.

.DESCRIPTION
    Builds a clean release (no dev files, no secrets), uploads it as a single archive, extracts it
    on the server, keeps .env and public/uploads untouched, fixes permissions and runs migrations.

    Nothing is deleted on the server: files you removed locally stay there until cleaned by hand.
    A timestamped backup of the current code and database is taken before each deploy.

.PARAMETER Server      Server IP or hostname.
.PARAMETER User        SSH username.
.PARAMETER RemotePath  Absolute path of the project root on the server (document root = <RemotePath>/public).
.PARAMETER Port        SSH port (default 22).
.PARAMETER KeyFile     Private key to authenticate with (recommended). Omit to use password/agent auth.
.PARAMETER EnvFile     Local .env to upload - only used on the FIRST deploy, never overwrites an existing one.
.PARAMETER Php         PHP binary on the server (default "php"; some hosts need e.g. /usr/local/bin/php8.2).
.PARAMETER Url         Public URL, used for the smoke test after deploying.
.PARAMETER SkipMigrate Skip `database/migrate.php`.
.PARAMETER DryRun      Build the release and show what would happen, without touching the server.

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File tools\deploy.ps1 `
        -Server 203.0.113.10 -User deploy -RemotePath /home/deploy/dubaitowingexperts `
        -Port 22 -KeyFile $HOME\.ssh\dte_deploy -Url https://dubaitowingexperts.com
#>
[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)][string]$Server,
    [Parameter(Mandatory = $true)][string]$User,
    [Parameter(Mandatory = $true)][string]$RemotePath,
    [int]$Port = 22,
    [string]$KeyFile = '',
    [string]$EnvFile = '',
    [string]$Php = 'php',
    [string]$Url = '',
    [switch]$SkipMigrate,
    [switch]$DryRun
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$stage = Join-Path $env:TEMP "dte-release-$stamp"
$archive = Join-Path $env:TEMP "dte-release-$stamp.tar.gz"

# Everything the server does NOT need: dev tooling, docs, secrets, local state, source artwork.
$excludeDirs = @('.git', '.claude', 'docs', 'tools', 'tests', 'node_modules')
$excludeFiles = @('.env', '.gitignore', '*.log', '*.zip', '*.tar.gz')

function Say($text, $colour = 'Gray') { Write-Host $text -ForegroundColor $colour }

# ---------------------------------------------------------------- pre-flight
Say "`n== Pre-flight ==" 'Cyan'
foreach ($required in @('public\index.php', 'app\bootstrap.php', 'database\migrate.php', 'config\business.php')) {
    if (-not (Test-Path (Join-Path $root $required))) { throw "Missing $required - is this the project root?" }
}
if (-not (Get-Command ssh -ErrorAction SilentlyContinue)) { throw 'ssh not found. Install the Windows OpenSSH client.' }
Say "  project root: $root"
Say "  target:       $User@$Server`:$Port  ->  $RemotePath"

# ---------------------------------------------------------------- build
Say "`n== Building release ==" 'Cyan'
New-Item -ItemType Directory -Force -Path $stage | Out-Null
$roboArgs = @($root, $stage, '/E', '/NFL', '/NDL', '/NJH', '/NJS', '/NP')
foreach ($d in $excludeDirs) { $roboArgs += @('/XD', (Join-Path $root $d)) }
$roboArgs += @('/XD', (Join-Path $root 'storage\logs'), '/XD', (Join-Path $root 'storage\cache'), '/XD', (Join-Path $root 'public\uploads'))
foreach ($f in $excludeFiles) { $roboArgs += @('/XF', $f) }
& robocopy @roboArgs | Out-Null
if ($LASTEXITCODE -ge 8) { throw "robocopy failed with exit code $LASTEXITCODE" }

# Markdown and the original brief are not needed in production
Get-ChildItem $stage -Filter *.md -File | Remove-Item -Force -ErrorAction SilentlyContinue
Get-ChildItem $stage -Filter '*Master_Website_Plan*' -File | Remove-Item -Force -ErrorAction SilentlyContinue
# Keep the writable directories present but empty
foreach ($d in @('storage\logs', 'storage\cache', 'public\uploads')) {
    New-Item -ItemType Directory -Force -Path (Join-Path $stage $d) | Out-Null
}
Copy-Item (Join-Path $root 'public\uploads\.htaccess') (Join-Path $stage 'public\uploads\.htaccess') -ErrorAction SilentlyContinue

$fileCount = (Get-ChildItem $stage -Recurse -File).Count
tar -czf $archive -C $stage .
$sizeMb = [math]::Round((Get-Item $archive).Length / 1MB, 2)
Say "  $fileCount files, archive $sizeMb MB"

if ($DryRun) {
    Say "`nDry run - archive left at $archive, server untouched." 'Yellow'
    return
}

# ---------------------------------------------------------------- upload
$sshArgs = @('-p', $Port)
$scpArgs = @('-P', $Port)
if ($KeyFile -ne '') {
    if (-not (Test-Path $KeyFile)) { throw "Key file not found: $KeyFile" }
    $sshArgs += @('-i', $KeyFile); $scpArgs += @('-i', $KeyFile)
}
$target = "$User@$Server"

Say "`n== Uploading ==" 'Cyan'
& scp @scpArgs $archive "${target}:/tmp/dte-release.tar.gz"
if ($LASTEXITCODE -ne 0) { throw 'Upload failed.' }

if ($EnvFile -ne '') {
    if (-not (Test-Path $EnvFile)) { throw "Env file not found: $EnvFile" }
    & scp @scpArgs $EnvFile "${target}:/tmp/dte.env"
    if ($LASTEXITCODE -ne 0) { throw 'Env upload failed.' }
}

# ---------------------------------------------------------------- install
Say "`n== Installing on the server ==" 'Cyan'
$migrateCmd = if ($SkipMigrate) { 'echo "migrations skipped"' } else { "$Php database/migrate.php" }
$envCmd = if ($EnvFile -ne '') { 'if [ ! -f .env ]; then mv /tmp/dte.env .env && chmod 600 .env && echo ".env installed"; else rm -f /tmp/dte.env && echo ".env already exists - kept"; fi' } else { 'true' }

# Single-quoted here-string: the shell script is passed through verbatim (no PowerShell
# interpolation), with the few values we need substituted afterwards.
$remoteTemplate = @'
set -e
mkdir -p '{{PATH}}'
cd '{{PATH}}'

# Back up the current code and database before changing anything
if [ -f public/index.php ]; then
  mkdir -p ../dte-backups
  tar -czf "../dte-backups/code-{{STAMP}}.tar.gz" --exclude='./public/uploads' . 2>/dev/null || true
  echo "code backup: ../dte-backups/code-{{STAMP}}.tar.gz"
  if [ -f .env ]; then
    DB_NAME=$(grep '^DB_DATABASE=' .env | cut -d= -f2- | tr -d '\r')
    DB_USER=$(grep '^DB_USERNAME=' .env | cut -d= -f2- | tr -d '\r')
    DB_PASS=$(grep '^DB_PASSWORD=' .env | cut -d= -f2- | tr -d '\r')
    if command -v mysqldump >/dev/null 2>&1 && [ -n "$DB_NAME" ]; then
      mysqldump --single-transaction -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "../dte-backups/db-{{STAMP}}.sql" 2>/dev/null \
        && echo "db backup: ../dte-backups/db-{{STAMP}}.sql" || echo "db backup skipped (check credentials)"
    fi
  fi
fi

tar -xzf /tmp/dte-release.tar.gz -C '{{PATH}}'
rm -f /tmp/dte-release.tar.gz
{{ENVCMD}}

mkdir -p storage/logs storage/cache public/uploads
chmod -R 775 storage public/uploads 2>/dev/null || true
find . -type f -name '*.php' -exec chmod 644 {} \; 2>/dev/null || true

if [ ! -f .env ]; then
  echo "WARNING: no .env on the server - create it before the site will run"
else
  {{MIGRATE}}
fi

echo "deployed: $(date)"
'@

$remote = $remoteTemplate.
    Replace('{{PATH}}', $RemotePath).
    Replace('{{STAMP}}', $stamp).
    Replace('{{ENVCMD}}', $envCmd).
    Replace('{{MIGRATE}}', $migrateCmd)

# Write the script to a temp file and redirect it in: piping from PowerShell prepends a UTF-8 BOM,
# which bash would read as part of the first command.
$remoteScript = Join-Path $env:TEMP "dte-remote-$stamp.sh"
[System.IO.File]::WriteAllText($remoteScript, ($remote -replace "`r`n", "`n"), (New-Object System.Text.ASCIIEncoding))
# Quote every argument: key paths routinely contain spaces (e.g. C:\Users\One Click\.ssh\...).
$quoted = $sshArgs | ForEach-Object { '"' + $_ + '"' }
$sshCmd = 'ssh ' + ($quoted -join ' ') + " `"$target`" `"bash -s`" < `"$remoteScript`""
& cmd /c $sshCmd
$sshExit = $LASTEXITCODE
Remove-Item $remoteScript -Force -ErrorAction SilentlyContinue
if ($sshExit -ne 0) { throw 'Remote install failed - check the output above. The backup in ../dte-backups can be restored.' }

# ---------------------------------------------------------------- verify
if ($Url -ne '') {
    Say "`n== Smoke test ==" 'Cyan'
    foreach ($path in @('/', '/services/car-recovery/', '/sitemap.xml', '/robots.txt', '/admin/login/')) {
        try {
            $r = Invoke-WebRequest -Uri ($Url.TrimEnd('/') + $path) -UseBasicParsing -TimeoutSec 25
            Say ("  {0,-28} {1}" -f $path, $r.StatusCode) 'Green'
        } catch {
            $code = if ($_.Exception.Response) { $_.Exception.Response.StatusCode.value__ } else { 'no response' }
            Say ("  {0,-28} {1}" -f $path, $code) 'Red'
        }
    }
}

Remove-Item $stage -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item $archive -Force -ErrorAction SilentlyContinue
Say "`nDone." 'Green'
Say "Next: run the SEO audit against the live site - php tests/seo-audit.php $Url"
