#!/usr/bin/env bash
# Deploys a Cleanup release zip to a WordPress site's plugins directory.
# Run from the repository root:
#   bash dev/deploy.sh /path/to/site --dry-run
# Options: --zip <path>  --dry-run  --force

set -euo pipefail

if ! command -v unzip >/dev/null 2>&1; then
    echo "deploy: unzip is required" >&2
    exit 1
fi

site_path=""
zip_path=""
dry_run=false
force=false

while [[ $# -gt 0 ]]; do
    case "$1" in
        --zip)
            zip_path=${2:-}
            shift 2
            ;;
        --dry-run)
            dry_run=true
            shift
            ;;
        --force)
            force=true
            shift
            ;;
        -h|--help)
            echo "Usage: bash dev/deploy.sh <site-path> [--zip <path>] [--dry-run] [--force]"
            exit 0
            ;;
        *)
            if [[ -z "$site_path" ]]; then
                site_path=$1
                shift
            else
                echo "deploy: unknown argument: $1" >&2
                exit 1
            fi
            ;;
    esac
done

if [[ -z "$site_path" ]]; then
    echo "Usage: bash dev/deploy.sh <site-path> [--zip <path>] [--dry-run] [--force]" >&2
    exit 1
fi

root=$(pwd)

if [[ -z "$zip_path" ]]; then
    zip_path=$(find "$root/dist" -maxdepth 1 -type f -name 'cleanup-v*.zip' -printf '%T@ %p\n' 2>/dev/null \
        | sort -nr | head -n 1 | cut -d' ' -f2-)
fi

if [[ -z "$zip_path" || ! -f "$zip_path" ]]; then
    echo "deploy: no release zip found in dist/. Run dev/build-release.sh first." >&2
    exit 1
fi

if [[ ! -d "$site_path" ]]; then
    echo "deploy: site path not found: $site_path" >&2
    exit 1
fi

site=$(cd "$site_path" && pwd)
plugins="$site/wp-content/plugins"
target="$plugins/cleanup"

echo "Site:    $site"
echo "Zip:     $zip_path"
echo "Target:  $target"

if [[ "$dry_run" == true ]]; then
    echo "Dry run only - no changes made."
    exit 0
fi

if [[ "$force" != true ]]; then
    read -r -p "Replace $target? [y/N] " answer
    if [[ ! "$answer" =~ ^[Yy]([Ee][Ss])?$ ]]; then
        echo "Cancelled."
        exit 0
    fi
fi

mkdir -p "$plugins"

if [[ -d "$target" ]]; then
    backup="${target}-backup-$(date +%Y%m%d-%H%M%S)"
    mv "$target" "$backup"
    echo "Backup:  $backup"
fi

stage=$(mktemp -d "${TMPDIR:-/tmp}/cleanup-deploy-XXXXXX")
trap 'rm -rf "$stage"' EXIT

unzip -q "$zip_path" -d "$stage"
mkdir -p "$target"
cp -R "$stage"/. "$target"/

echo "Deployed. Flush any object/page cache on the site if applicable."
