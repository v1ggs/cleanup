# TODO

Active backlog for the Cleanup plugin. `DEVELOPMENT.md` holds the **current
rules**; this file holds future work only. Tick an item when it is done and
update `DEVELOPMENT.md` if a rule changes.

## Tooling

- [x] Add PHPUnit with Brain Monkey; configure `phpunit.xml`,
      `tests/bootstrap.php` and `tests/TestCase.php`. No WP test library needed.
- [x] Add `composer test` and expand `composer check` to run PHPCS + tests.
- [ ] Add `composer lint` if a separate syntax check becomes useful.
- [x] Add feature tests for all eight config keys.
- [x] Add a CI workflow: PHP 8.2/8.3/8.4/8.5; runs `composer check` and
      `composer audit`.
- [x] Add a release packaging helper based on `DEVELOPMENT.md §8`:
      `dev/build-release.ps1`.
- [x] Add deployment scripting: `dev/deploy.ps1` deploys a release zip to a
      site's plugins directory with a backup.
- [x] Create the remaining root release files: `SECURITY.md`, `CHANGELOG.md`.

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

## Features

- [x] Add the plugin header to `cleanup/cleanup.php`. No Text Domain and no
      `languages/` folder.
- [x] Confirm PHPCS scope for `dev/`: linted locally, excluded from the zip.
- [x] Decide the file-level feature contract: kebab-case filenames, snake_case
      functions in `Cleanup`, `function name(bool|string|array $value): void`,
      ABSPATH guard and DocBlock. Documented in `DEVELOPMENT.md §6`.
- [x] Port the first feature: `hide_admin_bar` in
      `cleanup/includes/features/hide-admin-bar.php`.
- [x] Port `howdy_text` in `cleanup/includes/features/howdy-text.php`.
- [x] Port `admin_footer_text` in
      `cleanup/includes/features/admin-footer-text.php`.
- [x] Port `auto_featured_image` in
      `cleanup/includes/features/auto-featured-image.php`.
- [x] Port `dequeue_frontend_jquery` in
      `cleanup/includes/features/dequeue-frontend-jquery.php`.
- [x] Port `disable_customizer` in
      `cleanup/includes/features/disable-customizer.php`.
- [x] Port `disable_gutenberg` in
      `cleanup/includes/features/disable-gutenberg.php` (global styles merged).
- [x] Confirm the test structure once PHPUnit is set up.

## Later / optional

- [ ] Multisite / network-admin behaviour.
- [ ] Translation support, only if UI strings ever appear.
- [ ] Public asset URLs or a server alias, only if the plugin ever needs
      shipped assets.
