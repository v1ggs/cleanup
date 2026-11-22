# Releasing

Cleanup is distributed through **GitHub Releases only**. There is no WP.org
release process.

A release is needed only when the shipped plugin code in `cleanup/` changes.
Dev-only changes (helpers, docs, CI, tests) should just be committed and pushed
to `main`.

## Manual release checklist

1. **Choose the version.** Use `vX.Y.Z` — bugfix `0.1.1`, new feature `0.2.0`,
   breaking change `1.0.0`. Check that the tag does not already exist:

   ```sh
   git tag --list
   git ls-remote --tags origin
   ```

2. **Start from a clean tree.** Commit and push ordinary changes first.

   ```sh
   git status --short
   ```

3. **Bump the plugin header.** Set `Version:` in `cleanup/cleanup.php` to the
   release version without the `v` prefix, e.g. `0.1.1`.

4. **Update the changelog.** Promote `## [Unreleased]` in `CHANGELOG.md` to:

   ```md
   ## [0.1.1] - YYYY-MM-DD
   ```

   Keep an empty `## [Unreleased]` at the top if desired. Use Conventional
   Commit subjects since the previous tag as the source of entries.

5. **Run checks.**

   ```sh
   composer check
   composer audit
   ```

6. **Build the release zip.**

   ```powershell
   # Windows
   composer install --no-dev --no-interaction
   powershell -ExecutionPolicy Bypass -File .\dev\build-release.ps1
   ```

   ```sh
   # Linux/macOS
   composer install --no-dev --no-interaction
   bash dev/build-release.sh
   ```

   Both write `dist/cleanup-vX.Y.Z.zip`.

7. **Verify the zip contents.** It must contain only:

   ```text
   cleanup.php
   includes/
   LICENSE
   THIRD-PARTY-NOTICES.md
   ```

   No docs, dev tools, snippets, tests, WordPress core, `vendor/` or Composer
   files.

   ```sh
   # Linux/macOS
   unzip -Z1 dist/cleanup-v*.zip

   # Windows PowerShell
   Add-Type -AssemblyName System.IO.Compression.FileSystem
   $zip = Get-ChildItem dist -Filter 'cleanup-v*.zip' |
       Sort-Object LastWriteTime -Descending | Select-Object -First 1
   $archive = [System.IO.Compression.ZipFile]::OpenRead($zip.FullName)
   $archive.Entries.FullName
   $archive.Dispose()
   ```

8. **Commit release prep.**

   ```sh
   git add cleanup/cleanup.php CHANGELOG.md
   git -c user.name=OpenCode \
       -c user.email=90190553+v1ggs@users.noreply.github.com \
       commit -m "chore: prepare vX.Y.Z"
   ```

9. **Push the commit and tag explicitly.** The tags are lightweight;
   `git push --follow-tags` will not push them.

   ```sh
   git tag vX.Y.Z
   git push origin main
   git push origin vX.Y.Z
   ```

10. **Create the GitHub Release.** Attach the zip and use the CHANGELOG entry
    as the release notes.

    ```sh
    gh release create vX.Y.Z dist/cleanup-vX.Y.Z.zip \
      --title "vX.Y.Z" \
      --notes-file <path-to-release-notes>
    ```

    Or use the GitHub web UI: Releases → Draft new release → choose the tag →
    attach the ZIP → Publish.

11. **Verify and report.**

    ```sh
    gh release view vX.Y.Z
    git status --short
    git status -sb
    ```

    State clearly what was committed, tagged, pushed and published.

## Safety rules

- Never publish without operator approval.
- Never auto-install the ZIP on any website; updates are manual.
- Keep `enable_updates` off by default. Forks maintain their own update source.
- The plugin is shared as is, without support or guarantees.
