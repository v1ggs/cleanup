---
name: Release Cleanup
description: Prepare and publish a Cleanup plugin release - version sync, changelog, zip, tag, GitHub Release.
---

# Release Cleanup

Use when cutting a release. Root `DEVELOPMENT.md §8` is the source of truth;
read it before acting.

## Workflow

1. Confirm the working tree is clean and `composer check` passes.
2. Agree the version with the operator. Do not bump anything by hand outside
   this workflow.
3. Update the `Version:` header in `cleanup/cleanup.php` to match the `vX.Y.Z`
   tag. If that file is missing, stop and report that.
4. Update `CHANGELOG.md` with the changes since the previous tag. Use
   Conventional Commit subjects as the source.
5. Re-run `composer check`. Run `composer audit` separately if the network is
   available.
6. Run `composer install --no-dev`, then build the zip from the **`cleanup/`
   plugin folder only**. Stage `cleanup.php`, `includes/`, `LICENSE` and
   `THIRD-PARTY-NOTICES.md`. Never include root docs, `dev/`, `snippets/`,
   `tests/`, WordPress core, `vendor/` or Composer files.
7. Verify the zip contains only the plugin folder contents — no docs, dev
   tools, snippets or WordPress core.
8. Report the proposed tag, changelog and zip contents. **Ask for explicit
   approval before committing, tagging or pushing.** Never push without it.
9. After approval: commit, tag `vX.Y.Z`, `git push --follow-tags`, then create
   the GitHub Release with notes and the zip attached.

Report exactly what was committed, tagged and pushed.
