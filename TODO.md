# TODO

Active backlog for the Cleanup plugin. `DEVELOPMENT.md` holds the **current
rules**; this file holds future work only. Tick an item when it is done and
update `DEVELOPMENT.md` if a rule changes.

## Tooling

- [ ] Add PHPUnit with `WP_UnitTestCase`; configure `phpunit.xml` and the local
      WordPress test-library path.
- [ ] Add `composer lint` and `composer test` scripts.
- [ ] Expand `composer check` to include lint, tests and audit once those
      scripts exist.
- [ ] Add a CI workflow: PHP 8.2/8.3/8.4/8.5 × WordPress 7.x and current
      stable; run `composer phpcs` and `composer audit` (plus lint/tests when
      they exist).
- [ ] Add a release packaging helper / `.distignore` equivalent based on the
      ship list in `DEVELOPMENT.md §8`.
- [ ] Add deployment scripting so a published fix can reach client sites
      quickly.
- [ ] Create the remaining root release files: `SECURITY.md`, `CHANGELOG.md`.
      `LICENSE` and `THIRD-PARTY-NOTICES.md` already exist inside `cleanup/`.

## PHPCS backlog

Enable these analysis sniffs when the matching feature appears, so they are not
forgotten:

- `WordPress.WP.GlobalVariablesOverride`
- `WordPress.WP.CronInterval`
- `WordPress.WP.PostsPerPage`
- `WordPress.WP.DiscouragedConstants`
- `WordPress.WP.DiscouragedFunctions`
- `WordPress.WP.EnqueuedResources`
- `WordPress.WP.EnqueuedResourceParameters`
- `WordPress.WP.ClassNameCase`
- `WordPress.DateTime.RestrictedFunctions`
- `WordPress.DateTime.CurrentTimeTimestamp`
- `WordPress.CodeAnalysis.AssignmentInTernaryCondition`
- `WordPress.CodeAnalysis.EscapedNotTranslated`
- `WordPress.PHP.DontExtract`
- `WordPress.PHP.NoSilencedErrors`
- `WordPress.PHP.RestrictedPHPFunctions`
- `WordPress.PHP.IniSet`
- `WordPress.PHP.POSIXFunctions`
- `WordPress.PHP.PregQuoteDelimiter`
- `WordPress.DB.PreparedSQL`
- `WordPress.DB.RestrictedClasses`
- `Generic.CodeAnalysis.*` sniffs from `WordPress-Extra`
- `WordPress.NamingConventions.PrefixAllGlobals`, configured with the settled
  `Cleanup` / `cleanup_` prefix

## First feature

- [x] Add the plugin header to `cleanup/cleanup.php`. No Text Domain and no
      `languages/` folder.
- [x] Confirm PHPCS scope for `dev/`: linted locally, excluded from the zip.
- [ ] Decide the file-level feature contract: file naming, function naming,
      signature, return value and DocBlock shape. The registry already defines
      each entry as `{ type, callback }`.
- [ ] Port the first feature into `cleanup/includes/features/`.
- [ ] Confirm the test structure once PHPUnit is set up.

## Later / optional

- [ ] Multisite / network-admin behaviour.
- [ ] Translation support, only if UI strings ever appear.
- [ ] Public asset URLs or a server alias, only if the plugin ever needs
      shipped assets.
