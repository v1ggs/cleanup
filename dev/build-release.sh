#!/usr/bin/env bash
# Builds a release zip from the cleanup/ plugin folder only.
# Run from the repository root:
#   bash dev/build-release.sh [version]

set -euo pipefail

if ! command -v zip >/dev/null 2>&1; then
    echo "build-release: zip is required" >&2
    exit 1
fi

root=$(pwd)
plugin_dir="$root/cleanup"
header="$plugin_dir/cleanup.php"

if [[ ! -f "$header" ]]; then
    echo "build-release: plugin header not found: $header" >&2
    exit 1
fi

version=${1:-}
if [[ -z "$version" ]]; then
    version=$(sed -nE 's/^[[:space:]]*\*[[:space:]]*Version:[[:space:]]*([^[:space:]]+).*/\1/p' "$header" | head -n 1)
fi

if [[ -z "$version" ]]; then
    echo "build-release: could not read Version: from cleanup/cleanup.php; pass a version argument" >&2
    exit 1
fi

stage=$(mktemp -d "${TMPDIR:-/tmp}/cleanup-release-XXXXXX")
trap 'rm -rf "$stage"' EXIT

dist="$root/dist"
zip_path="$dist/cleanup-v${version}.zip"
items=(cleanup.php includes LICENSE THIRD-PARTY-NOTICES.md)

for item in "${items[@]}"; do
    if [[ ! -e "$plugin_dir/$item" ]]; then
        echo "build-release: missing release item: $plugin_dir/$item" >&2
        exit 1
    fi

    cp -R "$plugin_dir/$item" "$stage/"
done

mkdir -p "$dist"
rm -f "$zip_path"

(
    cd "$stage"
    zip -q -r "$zip_path" .
)

echo "Release zip: $zip_path"
