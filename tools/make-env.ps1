<#
.SYNOPSIS
    Creates the production .env (with a fresh APP_KEY) ready to upload with tools\deploy.ps1.

.DESCRIPTION
    Writes deploy\.env.production, which is git-ignored. It is uploaded once, on the first deploy
    (`-EnvFile deploy\.env.production`); after that the server's own .env is never overwritten.

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File tools\make-env.ps1 `
        -Url https://dubaitowingexperts.com -DbName dte_live -DbUser dte_user -NotifyEmail owner@example.com
#>
[CmdletBinding()]
param(
    [string]$Url = 'https://dubaitowingexperts.com',
    [Parameter(Mandatory = $true)][string]$DbName,
    [Parameter(Mandatory = $true)][string]$DbUser,
    [string]$DbHost = 'localhost',
    [int]$DbPort = 3306,
    [string]$NotifyEmail = '',
    [switch]$Staging
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$outDir = Join-Path $root 'deploy'
New-Item -ItemType Directory -Force -Path $outDir | Out-Null
$outFile = Join-Path $outDir '.env.production'

# The database password is typed here, never stored in a script or in shell history.
$secure = Read-Host -Prompt 'Database password' -AsSecureString
$dbPass = [Runtime.InteropServices.Marshal]::PtrToStringAuto(
    [Runtime.InteropServices.Marshal]::SecureStringToBSTR($secure))

$bytes = New-Object byte[] 32
[System.Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($bytes)
$appKey = -join ($bytes | ForEach-Object { $_.ToString('x2') })

$env = @"
APP_ENV=$(if ($Staging) { 'staging' } else { 'production' })
APP_DEBUG=false
APP_URL=$($Url.TrimEnd('/'))
APP_KEY=$appKey

DB_HOST=$DbHost
DB_PORT=$DbPort
DB_DATABASE=$DbName
DB_USERNAME=$DbUser
DB_PASSWORD=$dbPass

MAIL_FROM=no-reply@$(([uri]$Url).Host)
MAIL_TO=$NotifyEmail

FORCE_NOINDEX=$(if ($Staging) { 'true' } else { 'false' })
"@

[System.IO.File]::WriteAllText($outFile, $env, (New-Object System.Text.UTF8Encoding $false))
Write-Host "Wrote $outFile" -ForegroundColor Green
Write-Host "APP_KEY generated (64 hex chars). Keep this file private - it is git-ignored." -ForegroundColor Yellow
if ($Staging) { Write-Host "Staging mode: FORCE_NOINDEX=true, so search engines will not index it." -ForegroundColor Yellow }
