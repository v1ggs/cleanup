---
name: Security Review
description: Audit the Cleanup working tree against its security policy and public-exposure rules.
---

# Security Review

Use before a release, after manual edits, or whenever asked whether a change is
safe. Root `DEVELOPMENT.md §0` and the security items in §11 are the source of
truth; read them first.

## Workflow

1. Inspect the working-tree diff, or the whole plugin if there is no git repo.
2. Check the design invariants: no admin menu, no settings page, no saved
   options, no AJAX/REST endpoints, no front-end output or fingerprint.
3. Check every shipped PHP file has an `ABSPATH` guard, a `Cleanup` namespace,
   `cleanup_` prefixes and a DocBlock on functions/classes.
4. Check config loading: unknown keys ignored, per-key type validation, no
   request-derived paths, safe fallback when config is absent or malformed.
5. Check the originality rule: no copied, translated or closely paraphrased
   code; no fetched code in shipped files. Check the license whitelist and
   `THIRD-PARTY-NOTICES.md` for anything copied.
6. Check for forbidden content: secrets, client names, hardcoded personal
   URLs, `die`/`var_dump`/`print_r`, direct `$wpdb` queries, arbitrary remote
   CSS/JS.
7. Run `composer check`. Run `composer audit` if the network is available.
8. Report findings by severity with file and line references. Do **not** modify
   files unless the operator asks.
