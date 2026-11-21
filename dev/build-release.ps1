# Builds a release zip from the cleanup/ plugin folder only.
# Run from the repository root:
#   powershell -ExecutionPolicy Bypass -File .\dev\build-release.ps1

param(
    [string] $Version = ''
)

$ErrorActionPreference = 'Stop'

$root = (Get-Location).Path
$pluginDir = Join-Path $root 'cleanup'
$header = Join-Path $pluginDir 'cleanup.php'

if (-not (Test-Path -LiteralPath $header)) {
    throw "Plugin header not found: $header"
}

if ([string]::IsNullOrWhiteSpace($Version)) {
    $raw = Get-Content -LiteralPath $header -Raw
    $match = [regex]::Match($raw, '(?m)^\s*\*\s*Version:\s*(\S+)\s*$')

    if (-not $match.Success) {
        throw 'Could not read Version: from cleanup/cleanup.php; pass -Version explicitly.'
    }

    $Version = $match.Groups[1].Value
}

$stage = Join-Path $env:TEMP ('cleanup-release-' + [guid]::NewGuid().ToString('N'))
$dist = Join-Path $root 'dist'
$zip = Join-Path $dist ("cleanup-v{0}.zip" -f $Version)

$items = @(
    'cleanup.php',
    'includes',
    'LICENSE',
    'THIRD-PARTY-NOTICES.md'
)

try {
    New-Item -ItemType Directory -Path $stage -Force | Out-Null

    foreach ($item in $items) {
        $source = Join-Path $pluginDir $item

        if (-not (Test-Path -LiteralPath $source)) {
            throw "Missing release item: $source"
        }

        Copy-Item -LiteralPath $source -Destination $stage -Recurse -Force
    }

    New-Item -ItemType Directory -Path $dist -Force | Out-Null

    if (Test-Path -LiteralPath $zip) {
        Remove-Item -LiteralPath $zip -Force
    }

    Compress-Archive -Path (Join-Path $stage '*') -DestinationPath $zip -CompressionLevel Optimal

    Write-Host "Release zip: $zip"
} finally {
    if (Test-Path -LiteralPath $stage) {
        Remove-Item -LiteralPath $stage -Recurse -Force
    }
}
