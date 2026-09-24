<#
.SYNOPSIS
    Starts (or stops) the local development server for the Dubai Recovery Experts website.

.EXAMPLE
    powershell -ExecutionPolicy Bypass -File tools\serve.ps1
    powershell -ExecutionPolicy Bypass -File tools\serve.ps1 -Port 8081
    powershell -ExecutionPolicy Bypass -File tools\serve.ps1 -Stop

.NOTES
    Needs PHP 8.1+ with pdo_mysql, mbstring, gd, fileinfo and dom, plus a MySQL/MariaDB server
    matching the DB_* values in .env. Use -PhpPath if php.exe is not on PATH.
#>
[CmdletBinding()]
param(
    [int]$Port = 8080,
    [string]$PhpPath = '',
    [switch]$Stop
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot

if ($Stop) {
    $listener = Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue
    if ($listener) {
        Stop-Process -Id $listener.OwningProcess -Force
        Write-Host "Stopped the server on port $Port."
    } else {
        Write-Host "Nothing is listening on port $Port."
    }
    return
}

# Find PHP: the -PhpPath argument, then PATH, then a portable build under tools\php\.
if ($PhpPath -eq '') {
    $onPath = Get-Command php -ErrorAction SilentlyContinue
    if ($onPath) {
        $PhpPath = $onPath.Source
    } elseif (Test-Path "$root\tools\php\php.exe") {
        $PhpPath = "$root\tools\php\php.exe"
    }
}
if ($PhpPath -eq '' -or -not (Test-Path $PhpPath)) {
    Write-Error "PHP was not found. Install PHP 8.1+, or unzip a Windows build into tools\php\, or pass -PhpPath C:\path\to\php.exe"
    return
}

if (-not (Test-Path "$root\.env")) {
    Write-Error "No .env file. Copy .env.example to .env and set DB_* and APP_KEY first."
    return
}

$busy = Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue
if ($busy) {
    Write-Error "Port $Port is already in use (PID $($busy.OwningProcess)). Use -Port or run with -Stop first."
    return
}

Write-Host "PHP:  $PhpPath"
Write-Host "Site: http://127.0.0.1:$Port/"
Write-Host "Admin http://127.0.0.1:$Port/admin/login/"
Write-Host "Press Ctrl+C to stop." -ForegroundColor DarkGray

& $PhpPath -S "127.0.0.1:$Port" -t "$root\public" "$root\public\router-dev.php"
