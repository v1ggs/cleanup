# Changelog

All notable changes to this project are documented here. Versions follow the
plugin header `Version:` and the GitHub Release tag (`vX.Y.Z`).

## [Unreleased]

### Added

- Linux/macOS equivalents of the release and deployment helpers:
  `dev/build-release.sh` and `dev/deploy.sh`.
- `disable_xmlrpc` — disables XML-RPC with core filters.
- `restrict_rest_users` — removes public user routes for logged-out requests.
- `head_cleanup` — removes generated metadata from the head and HTTP headers.

## [0.1.0] - 2026-11-22

### Added

- Config loader for a flat, typed theme config file (`cleanup-config.php`),
  with `CLEANUP_CONFIG` constant and `cleanup_defaults` filter fallbacks.
- Feature registry in `cleanup/includes/features.php`.
- `hide_admin_bar` — hides the WordPress toolbar on the public website.
- `howdy_text` — replaces the default admin-bar greeting.
- `admin_footer_text` — replaces the admin footer with text or an optional link.
- `auto_featured_image` — assigns the first attached image as the featured image.
- `dequeue_frontend_jquery` — removes front-end jQuery, leaving admin jQuery intact.
- `disable_customizer` — fully removes the Customizer using core APIs.
- `disable_gutenberg` — disables the block editor and removes block/global styles.
- Tracked Git hooks for PHPCS and Conventional Commits.
- `SECURITY.md` and this changelog.
- Release packaging helper (`dev/build-release.ps1`) that builds a zip from
  the `cleanup/` folder only.
- PHPUnit + Brain Monkey test harness with config, registry and tests for all
  seven features.
- GitHub Actions CI workflow for PHP 8.2–8.5 running PHPCS, PHPUnit and audit.
- Deployment helper (`dev/deploy.ps1`) that backs up and replaces the plugin
  on a WordPress site from a release zip.

### Fixed

- Pre-commit hook now locates PHP in Git hook environments where devbox shims
  are not on `PATH`.
