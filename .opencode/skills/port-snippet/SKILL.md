---
name: Port Snippet
description: Port a reference snippet from snippets/ into the Cleanup plugin as a config-gated feature.
---

# Port Snippet

Use this when adding a feature to the Cleanup plugin from a file in `snippets/`.

All referenced documents are at the **repository root**, not inside this skill
directory.

## Workflow

1. Confirm the source file is in `snippets/` and its feature key is listed in
   the feature manifest in root `DEVELOPMENT.md §6`. If either is missing, stop
   and ask — never invent a config key.
2. Read root `DEVELOPMENT.md §0` (security policy, public exposure policy,
   originality rule) and §4 (code style).
3. Write the feature from scratch using WordPress core APIs. Never copy,
   translate or closely paraphrase the reference snippet, and never fetch code
   from the internet into a shipped file.
4. Implement the feature:
   - `Cleanup` namespace, unique names, `cleanup_` prefix for hooks/config.
   - `defined( 'ABSPATH' ) || exit;` guard and DocBlocks on every function/class.
   - Config key with per-key schema validation, defaulting to off. No UI, no
     saved options, no public endpoints, no front-end fingerprint.
   - Escape all config text with `esc_html()` / `esc_url()`.
   - Add the feature file under `cleanup/includes/features/` and set its
     callback in `cleanup/includes/features.php`; hooks are attached at plugin
     file load, before `plugins_loaded`.
   - New-site-only features must be configurable only, never toggleable in UI.
5. Add a test from `DEVELOPMENT.md §5` — the per-feature template plus any
   feature-specific regression case from the table.
6. Run `composer check`. Do not report success until it passes.
7. Update root `README.md` in **both** places: add the new key to the PHP config
   example (with a value of the correct type) and to the feature table (with its
   value type, a one-line description and any "new websites only" warning). The
   example and the table must always contain the same keys.
8. Once the feature is verified and documented, remove the processed file from
   `snippets/` and delete its row from the manifest in `DEVELOPMENT.md §6`.
   That keeps `snippets/` an inbox of unprocessed ideas, not an archive.
