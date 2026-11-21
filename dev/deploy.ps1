# Deploys a Cleanup release zip to a WordPress site's plugins directory.
# Run from the repository root:
#   powershell -ExecutionPolicy Bypass -File .\dev\deploy.ps1 -SitePath C:\sites\example -DryRun
# Add -Force to skip the confirmation prompt.

param(
    [Parameter(Mandatory = $true)]
    [string] $SitePath,
    [string] $ZipPath = '',
    [switch] $DryRun,
    [switch] $Force
)

$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.IO.Compression.FileSystem

$root = (Get-Location).Path

if ([string]::IsNullOrWhiteSpace($ZipPath)) {
    $zip = Get-ChildItem (Join-Path $root 'dist') -Filter 'cleanup-v*.zip' -ErrorAction SilentlyContinue |
        Sort-Object LastWriteTime -Descending |
        Select-Object -First 1

    if ($null -eq $zip) {
        throw 'No release zip found in dist/. Run dev/build-release.ps1 first.'
    }

    $ZipPath = $zip.FullName
}

if (-not (Test-Path -LiteralPath $ZipPath)) {
    throw "Release zip not found: $ZipPath"
}

$site = (Resolve-Path -LiteralPath $SitePath -ErrorAction Stop).Path
$plugins = Join-Path $site 'wp-content\plugins'
$target = Join-Path $plugins 'cleanup'

Write-Host "Site:    $site"
Write-Host "Zip:     $ZipPath"
Write-Host "Target:  $target"

if ($DryRun) {
    Write-Host 'Dry run only — no changes made.'
    exit 0
}

if (-not $Force) {
    $answer = Read-Host "Replace $target? [y/N]"
    if ($answer -notmatch '^(y|yes)$') {
        Write-Host 'Cancelled.'
        exit 0
    }
}

if (-not (Test-Path -LiteralPath $plugins)) {
    New-Item -ItemType Directory -Path $plugins -Force | Out-Null
}

$backup = ''
if (Test-Path -LiteralPath $target) {
    $stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
    $backup = "$target-backup-$stamp"
    Move-Item -LiteralPath $target -Destination $backup
    Write-Host "Backup:  $backup"
}

$stage = Join-Path $env:TEMP ('cleanup-deploy-' + [guid]::NewGuid().ToString('N'))
try {
    New-Item -ItemType Directory -Path $stage -Force | Out-Null
    [System.IO.Compression.ZipFile]::ExtractToDirectory($ZipPath, $stage)
    New-Item -ItemType Directory -Path $target -Force | Out-Null
    Copy-Item -Path (Join-Path $stage '*') -Destination $target -Recurse -Force
} finally {
    if (Test-Path -LiteralPath $stage) {
        Remove-Item -LiteralPath $stage -Recurse -Force
    }
}

Write-Host 'Deployed. Flush any object/page cache on the site if applicable.'
