# Development guidelines — Cleanup

Practical rules for working on this repository. Keep this file current when the
tooling, scripts or decisions change. Future work lives in `TODO.md`; keep
rules here and tasks there.

The plugin is distributed through **GitHub Releases only** — not the
WordPress.org directory. There is no `wp-env` setup here; use your own local
WordPress installation for development and testing.

## Design summary

This is a **developer-only WordPress plugin**. It has no admin menu, no
settings page, no options, no saved data and no front-end output. A config file
in the custom theme is the **only** control surface. Everything is off unless
the config explicitly enables it.

The repository root is the WordPress **plugins directory**. The plugin itself
lives in `cleanup/` at that root; only that folder is shipped. Development
docs, hooks, tests, snippets and WordPress core stay at the repository root and
are never part of the release zip.

The name is settled: `cleanup`. Centralise these identifiers in the bootstrap
file so a future rename is a single edit:

- Namespace: `Cleanup`
- Slug: `cleanup` (no text domain and no translations — there are no UI strings)
- Hook / config prefix: `cleanup_`
- Constant prefix: `CLEANUP_`
- Theme config file: `cleanup-config.php`

## 0. Security policy (non-negotiable)

This plugin is intended for **real client sites, including production**.
Security and recoverability take priority over convenience.

- **Stock install = everything off.** With no project configuration present,
  no feature attaches any hook. A fresh GitHub install is inert.
- **Project configuration prepares features in advance.** A flat config file in
  the custom theme (see §2) enables features for a client site. Configuration
  is trusted developer code, not user input.
- **Features that change site structure or data** (currently only taxonomy
  detach) are **new-site-only** — apply them before content exists. They are
  enabled only from the config file.
- **Nothing user-supplied is processed.** There is no admin page, no menu, no
  capability check, no nonce, no saved option, no AJAX/REST endpoint and no
  form input. Do not add any of these. If a feature seems to need one, stop and
  escalate before implementing it.
- **No secrets in source or config.** No passwords, tokens or personal data
  in tracked files. Never add a query-string login gate — it is security
  theater and leaks the secret through history, referrers and logs.
- **Generated code must be original.** An agent may read any code for
  understanding, but must not copy, translate or closely paraphrase it into
  shipped files, and must not fetch code from the internet into shipped files.
  Every function/class is written from scratch using WordPress APIs and must be
  reviewable as such. The only exception is whitelisted code with a
  `THIRD-PARTY-NOTICES.md` entry (see §6).
- **Forbidden in shipped code:** `die()`/`var_dump()`/`print_r()` to screen,
  `eval`, `extract`, direct `$wpdb` queries, unprefixed globals, hardcoded
  URLs/passwords/post-type slugs, arbitrary remote CSS/JS loading.
- **Debug tools** live in `dev/`, are enabled only by explicit config plus
  `WP_DEBUG`, are excluded from the release zip, and write to `error_log` or
  WP-CLI output — never to an admin page and never via `die()`.
- **Abort direct access:** `defined( 'ABSPATH' ) || exit;` in every PHP file.
- **No uninstall file.** The plugin stores nothing, so there is nothing to
  clean up. Do not add `uninstall.php` unless the plugin starts writing data.
- **Namespace PHP symbols** to avoid collisions with other plugins, but give
  every function/class a unique name *within* the namespace as well — the old
  snippets declared `debug_admin_menu()` three times in `Cleanup`.
- **Prefix non-PHP identifiers** (hooks, config keys, config constants) with
  `cleanup_` / `CLEANUP_`; namespaces do not cover those.
- **WordPress API only; custom code is integration glue.** Never hand-roll
  sanitisation, escaping, redirects, URL/path building, HTTP or database
  access. Use the core functions and hooks. Keep custom code as small and
  obvious as possible: load the config, look up the feature, attach its hook.
- **PHPCS is a floor, not a sign-off.** `WordPress.Security` covers output
  escaping, nonce verification, validated/sanitised input and safe redirects,
  but it does not verify any of the policies above. A clean PHPCS run is
  necessary but not sufficient; add the tests in §5 as they become available.

### Public exposure policy

The repository is public on GitHub (not WordPress.org). Assume the source can
be read by anyone, including someone who finds the sites using it through your
portfolio. These are agent-facing rules — apply them on every change.

- **No front-end footprint.** Never print HTML, enqueue CSS/JS, expose a REST
  namespace, register a shortcode, or add a query-string API that leaves a
  fingerprint on the public site. An attacker who knows the plugin still
  cannot confirm which sites run it.
- **No shipped docs in the release zip.** Keep `README.md`, `CHANGELOG.md`,
  `SECURITY.md` and all dev files on GitHub only; exclude them from the built
  zip so they cannot be fetched from
  `/wp-content/plugins/<slug>/` as a fingerprint. `LICENSE` and
  `THIRD-PARTY-NOTICES.md` are legal/attribution files and **must** ship. PHP
  files carry `ABSPATH` guards, so direct requests output nothing.
- **No secrets, no client names.** No tokens, passwords, real client domains,
  or "sites using this plugin" lists in tracked files, examples, config
  templates or docs.
- **No public endpoints or data processing.** Do not add AJAX/REST actions,
  public forms, uploads, shortcodes or direct `$wpdb` queries. If a feature
  needs one, stop and escalate before implementing it.
- **Inert by default, config-only, no UI, no saved data** — as specified in the
  rules above.
- **Automated gates are mandatory.** PHPCS with `WordPress.Security` must pass.
  Tests asserting inert-by-default and config behaviour are in `TODO.md` and
  must pass once they exist. `composer audit` is a separate, network-dependent
  check.
- **Fast fix path.** Keep releases versioned so a fix can be tagged and
  distributed quickly. `SECURITY.md` gives reporters a private channel.

## 1. Stack and layout

- PHP 8.2+ (`phpcs.xml` has `testVersion=8.2-`), WordPress 7.x
  (`minimum_wp_version=7.0`).
- **Composer packages are tools only, never runtime libraries.** Dev tooling
  (PHPCS, WPCS, PHPCompatibilityWP) lives in `require-dev`; PHPUnit is in
  `TODO.md`. The plugin must not require, autoload or bundle
  any Composer package at runtime: no `vendor/autoload.php` in shipped code,
  no `require` entries in `composer.json`, and no `vendor/` in the release zip.
  A runtime library needs the **operator's explicit permission** (§10); then
  the §6 license whitelist, attribution rules and `composer audit` apply.
- `composer.json` declares `name` (`v1ggs/cleanup`), `description` and
  `license: MIT`, matching §10. It defines two scripts: `phpcs` (PHPCS with
  `phpcs.xml`) and `check` (currently an alias for `phpcs`; see `TODO.md` for
  planned expansion). `composer audit` is a built-in
  command, kept separate because it needs network access. Commit
  `composer.lock`; never hand-edit it. Composer may warn that the root version
  defaults to `1.0.0` until the first git tag — harmless.
- There is **no npm/Node toolchain**. Composer is the only dependency manager,
  and Git hooks are plain tracked shell scripts under `dev/hooks/`.
- `vendor/` (Composer) is ignored; hook scripts are tracked.
- Layout:

  ```
  cleanup/                  The shipped plugin folder
    cleanup.php             Plugin header, ABSPATH guard, bootstrap
    includes/
      config.php            Load and validate the theme config file
      features.php          Flat map of feature key => callback
      features/             One small file per feature
    LICENSE                 MIT license (ships)
    THIRD-PARTY-NOTICES.md  Attribution (ships)
  dev/                      Local-only debug tools; excluded from release zip
  dev/hooks/                Tracked Git hooks (no npm)
  snippets/                 Local inbox of unprocessed snippets (git-ignored)
  tests/                    PHPUnit + WP_UnitTestCase
  wordpress/                WordPress core / web root (git-ignored)
  wp-config.php             Root config, one level above ABSPATH
  .github/workflows/        CI + release workflows
  ```

  There is deliberately **no** `admin/`, `assets/`, `languages/` or
  `uninstall.php` inside `cleanup/`: there is no UI, no assets, no translations
  (so no text domain) and no stored data.

- GitHub-facing files at the root: `README.md`, `CHANGELOG.md`, `SECURITY.md`.
  `LICENSE` and `THIRD-PARTY-NOTICES.md` live inside `cleanup/` because they
  ship. No `readme.txt` / SVN assets — WordPress.org is out of scope.
- `snippets/` is a local, git-ignored inbox (see §6 for its lifecycle). It is
  neither reference material nor shipped plugin code, and is excluded from
  PHPCS and from the release zip.

## 2. Setup

1. `composer install` — installs PHPCS/WPCS and generates `vendor/`.
2. After `git init`, enable the tracked hooks once per clone:
   `git config core.hooksPath dev/hooks`.
3. Commit `composer.lock`; never commit `vendor/`, `.env`, logs or build
   artefacts.
4. Point the plugin at your own local WordPress installation; no `wp-env`.
5. No `.env` is needed for local work. Never commit credentials or tokens.

### Project layout notes

- **Web root: `wordpress/` at the project root.** Composer installs WordPress
  core there (`extra.wordpress-install-dir` in `composer.json`), and the web
  server serves that folder. If you switch to `public/`, change the Composer
  setting and the server document root together.
- `wp-config.php` lives in the project root, one level above `ABSPATH` —
  WordPress finds it there automatically. Because the web root is a subfolder,
  it cannot be requested directly. It can be committed, but DB credentials and
  salts must come from environment variables (`getenv()`), never tracked values.
- The project root **is** the WordPress plugins directory. The plugin lives in
  `cleanup/` inside it:

  ```php
  // wp-config.php, in the project root (the plugins directory)
  define( 'WP_PLUGIN_DIR', __DIR__ );
  define( 'WP_PLUGIN_URL', getenv( 'WP_PLUGIN_URL' ) );
  ```

  WordPress scans `WP_PLUGIN_DIR` and finds `cleanup/cleanup.php` by path.
  That folder is outside the web root and has no public URL, which is fine
  because the plugin ships no assets, UI or front-end output and never calls
  `plugins_url()`. If assets are added later, expose the folder via a server
  alias or symlink inside the web root.
- Because the project root is the plugins directory, WordPress scans every
  top-level folder and `.php` file there for plugin headers. Keep the root tidy;
  the only plugin header belongs in `cleanup/cleanup.php`.
- Keep `extra.wordpress-install-dir` in `composer.json` in sync with the web
  root. It is currently `wordpress`; if core moves, change this value too or the
  next install will put it back in `vendor/wordpress`.

### Project configuration file

The config file in the **custom theme** is the only way to enable features.

- Default location: `wp-content/themes/<your-theme>/cleanup-config.php`,
  resolved with `get_stylesheet_directory()` so a child theme can override the
  parent (`get_template_directory()` as fallback).
- Override the path with the `CLEANUP_CONFIG_FILE` constant if needed.
- The file returns a **flat array** of feature keys to **typed values**
  (boolean, string or array depending on the key), starts with
  `defined( 'ABSPATH' ) || exit;`, and outputs nothing:

  ```php
  <?php
  defined( 'ABSPATH' ) || exit;

  return [
      'hide_admin_bar'          => true,
      'howdy_text'              => 'Welcome',
      'admin_footer_text'       => 'Site name',
      'auto_featured_image'     => true,
      'dequeue_frontend_jquery' => true,
      'disable_customizer'      => true,
      'disable_gutenberg'       => true,
  ];
  ```

- `config.php` validates it strictly against a **per-key schema**: allowlist
  feature keys, then check the type and value for each key (bool, string, enum
  or a small array of fields). A wrong-typed value falls back to the default;
  unknown keys are ignored; never include a request-derived path. A malformed
  file must not fatal or emit output.
- Config text is developer-controlled but still escaped on output with
  `esc_html()` / `esc_url()`.
- **Precedence:** config value → safe off. There is no saved option, no
  `locked`/`code_only` metadata and no UI state to merge.
- Alternatives when a theme file is not appropriate: a `CLEANUP_CONFIG`
  array constant in `wp-config.php`, or a `cleanup_defaults` filter from an
  mu-plugin. Use one source only; the theme file wins if more than one is
  present.
- Because the config lives in the theme, swapping/deploying the theme swaps
  the config too. Keep it in the site's theme repository and never ship it in
  the plugin zip.

### When hooks are registered

- Load the config and attach every enabled feature's hooks **at plugin file
  load**, before the `plugins_loaded` action fires. Plugin files are included
  at that point, so features can safely hook `plugins_loaded`,
  `after_setup_theme`, `init`, `save_post` and similar.
- Do not defer registration to a later hook. An enabled feature whose hook has
  already fired silently does nothing.
- Unknown config keys are ignored. If a key is enabled but its feature file is
  missing, skip it silently in production — no fatal error, no output — and
  under `WP_DEBUG` write an `error_log()` message naming the missing feature.

### Constants that must live in `wp-config.php`

`DISALLOW_FILE_EDIT`, `FORCE_SSL_ADMIN` and similar must be defined before
WordPress loads plugins. Document them as copyable instructions in `README.md`
or `snippets/`; never define them from plugin code.

## 3. Everyday commands

| Command                                | What it does                            |
| -------------------------------------- | --------------------------------------- |
| `composer phpcs`                       | PHPCS with `phpcs.xml`                  |
| `composer check`                       | Alias for `composer phpcs` for now      |
| `composer audit`                       | Built-in dependency audit (needs network) |
| `composer phpcbf`                      | Auto-fix what PHPCS can fix             |
| `git config core.hooksPath dev/hooks`   | Enable tracked hooks (once per clone)   |

`lint` and `test` scripts do not exist yet — see `TODO.md`. PHPCS includes a
PHP syntax sniff. The pre-commit hook runs PHPCS offline via `php`;
`composer audit` is a separate, network-dependent check.

## 4. Code style

- `.editorconfig` is the source of truth: UTF-8, LF, trimmed trailing
  whitespace, final newline, 4 spaces for PHP, 80-column soft limit (Markdown
  unlimited).
- PSR-12 plus the selected rules in `phpcs.xml`: `WordPress.Security`,
  `WordPress.DB.PreparedSQLPlaceholders`, `WordPress.WP.AlternativeFunctions`,
  the deprecation sniffs, `PHPCompatibilityWP` (8.2–), and the WordPress docs
  sniffs (`Squiz.Commenting.FunctionComment`). `minimum_wp_version` is 7.0.
- Namespace all code under `Cleanup`; unique function/class names within
  that namespace. Prefix hook names, config keys and constants.
- **Every function, class and method has a DocBlock**: summary, `@param` for
  each parameter, `@return`, plus `@since` where useful.
- No `var_dump`/`die` in shipped code; no unescaped output; no unvalidated
  input; no direct database queries.
- Prefer core APIs over custom implementations (see §0). Part of this is
  machine-enforced by `WordPress.WP.AlternativeFunctions`,
  `WordPress.DB.DirectDatabaseQuery` and `WordPress.DB.RestrictedFunctions`.
- WPCS **style** sniffs stay disabled — PSR-12 owns formatting. Do not add
  WPCS indentation, parenthesis/array-spacing, short-array, Yoda-condition or
  `class-*.php` file-name sniffs, and do not add
  `WordPress.NamingConventions.ValidHookName`.
- **PHPCS backlog** — future analysis sniffs are listed in `TODO.md`; enable
  them when the matching feature exists. When enabling
  `WordPress.NamingConventions.PrefixAllGlobals`, configure it with the settled
  `Cleanup` / `cleanup_` prefix instead of leaving it disabled.

## 5. Tests

Test tooling and structure are in `TODO.md`; there is no `composer test` script
yet. The template and regression table below are the rules for feature tests
once the tooling exists.

### Global invariants (run on every suite)

These are regression tests for the policies in §0. Each must fail if a policy
is broken:

- **Inert by default:** with no config file, no feature hook is registered and
  no option is written.
- **No front-end footprint:** on a front-end request, the plugin adds no HTML,
  CSS, JS, REST route, AJAX action, shortcode or query parameter.
- **No UI or saved data:** no admin menu, no settings page, no option written,
  no nonce or capability check needed.
- **No runtime dependency:** no `vendor/autoload.php` is loaded, no Composer
  class from `require` is used.
- **ABSPATH guard:** every shipped PHP file aborts when loaded outside
  WordPress.
- **No debug leftovers:** no `die`, `var_dump` or `print_r` executes; PHPCS
  enforces this statically (§4).

### Configuration tests

Cover `cleanup/includes/config.php`:

- no config file → all features off;
- config from the custom theme via `get_stylesheet_directory()`, child-theme
  override via `get_template_directory()`, and `CLEANUP_CONFIG_FILE`
  override;
- `CLEANUP_CONFIG` constant in `wp-config.php` and the
  `cleanup_defaults` filter produce the same result; when several sources
  are present, the theme file wins;
- strict per-key validation of the flat map: unknown keys ignored, wrong-typed
  values fall back to defaults, request-derived paths rejected;
- malformed config does not fatal or emit output.

### Per-feature test template

Every feature has a test asserting: off by default, hook attached **only** when
its config key is enabled, correct hook and priority, and no output on the
front end.

### Feature-specific regression cases

These cover bugs found in the legacy snippets. Add the test with the feature:

| Feature             | Must assert                                                                                     |
| ------------------- | ----------------------------------------------------------------------------------------------- |
| Admin bar hide      | Only one `show_admin_bar` filter is live                                                        |
| Admin bar howdy     | Config text is escaped; only one `my-account` node is added                                     |
| Admin footer        | Config text and URL are escaped; no hardcoded personal URL ships                                |
| Featured image      | Autosaves, revisions and posts with an existing thumbnail are skipped                            |
| Theme supports      | Global invariant: `post-thumbnails` and `title-tag` are never removed                            |
| Dequeue jQuery      | Front end only; admin jQuery remains registered for CMB2                                        |
| Customizer disable  | `customize.php` is unreachable and no customizer link is left in the admin bar                   |
| Gutenberg disable   | Classic editor is used, global styles are removed, and no `register_post_type( 'your_cpt' )` example ships |

### CI

CI setup is in `TODO.md`; no workflow exists yet. Any future workflow and the
local hooks must inspect tool output for failure markers on machines where
devbox `.cmd` shims swallow child exit codes. Test runs must not modify the
local WordPress database in a way that leaks into another suite — use isolated
tables/prefixes per run.

## 6. WordPress compatibility and feature triage

- Never override core globals, never assume a plugin/theme is active, never
  hardcode a post type or menu slug that a config value should provide.
- No third-party library may be loaded by the plugin at runtime without the
  operator's explicit permission (see §1). None are currently present.
- `theme.json` is a **theme** file. Never apply it from the plugin.
- `menus.php` `wp_nav_menu()` calls are **theme template** code — docs only.
- `wp-config.php` constants are instructions only (see §2).
- New-site-only features (currently only `remove-default-taxonomy`) are enabled
  from config and documented as "apply before content exists".
- Permalinks are a setup step, not a plugin feature — configure them in the
  site's setup script or via WP-CLI, never from this plugin.
- Keep the classic-editor + CMB2 assumption in mind: Gutenberg may be enabled
  later, but FSE is not for client sites. Disabling the block editor must not
  break CMB2 fields.

### Snippet lifecycle

`snippets/` is a **local, git-ignored inbox**, not part of the plugin or the
repository. The workflow is:

1. Put a new source snippet directly in `snippets/` as a flat file. It is never
   shipped, linted or committed.
2. Use the `port-snippet` skill (or the rules in §0, §4, §5 and this manifest)
   to turn it into a config-gated feature in `cleanup/includes/features/`.
3. Once the feature is ported and verified, **delete the processed snippet**
   from `snippets/` and remove its row from the manifest below.

So `snippets/` only ever contains unprocessed input, and the manifest is a
to-do list rather than an archive. Source paths in it are local-only and will
not exist in a fresh clone.

### Feature manifest — copy checklist

All source paths are relative to `snippets/`. The reference files are
**references only**: rewrite each feature from scratch with WordPress APIs
(§0), never paste it verbatim. Tick a row once the feature is ported.

**Feature contract:** one file per feature in `cleanup/includes/features/`,
kebab-case filename, one `snake_case` function in the `Cleanup` namespace,
signature `function name(bool|string|array $value): void`, `ABSPATH` guard and
DocBlock required. The file is required from `includes/features.php`, which
sets the registry callback.

| Done | Config key                | Source snippet                          | Notes                                                       |
| ---- | ------------------------- | --------------------------------------- | ----------------------------------------------------------- |
| [x]  | `hide_admin_bar`          | `admin-bar-hide.php`                    | Hides the front-end bar for everyone via one filter         |
| [x]  | `howdy_text`              | `admin-bar-howdy.php`                   | Greeting + display name, both escaped                       |
| [x]  | `admin_footer_text`       | `admin-footer.php`                      | Plain text or optional link; both escaped                   |
| [x]  | `auto_featured_image`     | `featured-image-auto.php`               | Skips autosaves, revisions, existing thumbnails             |
| [ ]  | `remove_default_taxonomy` | `remove-default-taxonomy.php`           | New-site-only; orphans terms — confirm before enabling      |
| [x]  | `dequeue_frontend_jquery` | `dequeue-jquery.php`                    | Front-end only; admin jQuery untouched for CMB2             |
| [x]  | `disable_customizer`      | *write new*                             | Submenu, admin-bar node and direct URL guard                |
| [x]  | `disable_gutenberg`       | `editor-disable.php` + `dequeue-global-styles.php` | Block editor off; styles and global styles removed |

Out-of-scope snippets were deleted from the local inbox. Do not reintroduce one
without adding it to this manifest first.

### Build notes: `disable_customizer`

The Customizer is core functionality and is **not deprecated** — the installed
WordPress 7.1.5 still provides the `customize_loaded_components` filter,
`wp_admin_bar_customize_menu()` and `wp-admin/customize.php`. The WP 7.0/7.1/7.2
field guides show no removal or API break. Avoid exact core line numbers in this
document; they change between releases. Centralising the disable here is
preferable to repeating it in every theme.

`customize_loaded_components` alone is **not** a full disable — it only unloads
the widgets and nav-menus components. A complete disable needs three parts:

1. `remove_submenu_page( 'themes.php', 'customize.php' )` on `admin_menu`.
2. `remove_node( 'customize' )` on `admin_bar_menu` at a **late priority**
   (other plugins may re-add the link via `customize_register`; WP itself hides
   it for block themes without such callbacks).
3. On `admin_init`, guard direct `customize.php` access using the page slug and
   `wp_safe_redirect()` to `home_url( '/' )`, then `exit`.

Use **page slugs and admin-bar node IDs**, never CSS selectors — WP 7.2 replaced
Dashicons with inline SVG icons in the admin bar and menu, which breaks
selector-based hacks. No custom admin colour scheme or icon CSS may be added.

Keep the feature inert unless its config key is true, and add tests for all
three parts: submenu removed, node removed even when another callback adds it,
and direct `customize.php` redirected.

### Third-party code: license whitelist

**Generated code must be original.** An agent may read code for understanding,
but must not copy, translate or closely paraphrase it into shipped files, and
must not fetch code from the internet into shipped files. Every function/class
must be written from scratch using WordPress APIs and must be reviewable as such.

**Copying into shipped files (PHP, JS, CSS, fonts, images) is permitted only**
from sources on this whitelist. A repository with no license file is **not** on
the whitelist — being publicly readable does not grant permission to copy.

| License                    | Conditions                                                   |
| -------------------------- | ------------------------------------------------------------ |
| MIT                        | Keep the copyright notice and license text                   |
| BSD-2-Clause, BSD-3-Clause | Keep notice + license text (BSD-3: no endorsement)           |
| ISC                        | Keep the copyright notice and license text                   |
| Apache-2.0                 | Keep notice + license text, preserve `NOTICE`, state changes |
| MPL-2.0                    | Modified files stay under MPL; own files may use any license |
| CC0, Unlicense             | None                                                         |

- **BSD-3-Clause** additionally forbids using the copyright holder's or
  contributors' names to endorse or promote this plugin without permission.
  Keeping the full license text satisfies this — do not use their names in
  marketing or documentation.
- **MPL-2.0** requires the source of modified MPL files to remain available.
  Shipping the modified `.php` file satisfies this, so no separate upstream
  archive is needed; just keep the notices and license text in the zip.

**GPL and AGPL must not be copied into this plugin.** Incorporating them would
make the plugin a derivative work that must be relicensed under the same
license — a one-way door. Permissive code may be included in a GPL project, but
GPL code must not be included in a permissive one.

Every copied snippet **must** have its source URL and **full license text**
recorded in `THIRD-PARTY-NOTICES.md`, with the original copyright notice kept
inline in the file or in that document, and any changes noted. `README.md`
**must** point to `THIRD-PARTY-NOTICES.md` on GitHub, and that file **must** be
included in the release zip (see §8). Dev-only tooling (WPCS, PHPCS,
PHPCompatibility) does not affect this plugin's license: running a tool over
code does not make the output a derivative work, and `vendor/` is excluded from
the release zip.

## 7. Git hooks and commits

Git hooks are plain POSIX shell scripts tracked in `dev/hooks/` — no Husky, no
npm, no commitlint, and no generated hook files. Enable them once per clone:

```sh
git config core.hooksPath dev/hooks
```

That setting is local Git config (`.git/config`), so it is not committed; every
fresh clone must run it. Verify with `git config --get core.hooksPath`.

**`dev/hooks/pre-commit`** runs PHPCS offline, directly through `php` so it does
not depend on `composer` being on PATH. It accepts either `php` or `php.cmd`
(the latter matters for Git Bash on Windows) and fails the commit on any PHPCS
violation. It checks only the ruleset — `composer audit` stays a separate,
network-dependent command.

**`dev/hooks/commit-msg`** validates the first line of the message file Git
passes as `$1`. Accepted format:

```
type(scope): subject
```

- `type` is one of `feat`, `fix`, `docs`, `style`, `refactor`, `perf`, `test`,
  `build`, `ci`, `chore`, `revert`;
- `scope` is optional and lowercase (`a-z`, `0-9`, `.`, `_`, `-`, `/`);
- `subject` is 1–100 characters; no trailing period;
- the whole rule lives in the hook script — there is no commitlint config to
  drift out of sync.

Both hooks were tested with valid and invalid inputs. To test them manually
without committing:

```sh
# Git Bash on Windows: "C:\Program Files\Git\bin\sh.exe"
sh dev/hooks/commit-msg path/to/message-file
sh dev/hooks/pre-commit
```

An invalid message exits 1 with an explanation; a valid one exits 0.

### Commit authorship

Commits written by an AI agent use **the agent's own name** (e.g. `OpenCode`)
with **the maintainer's existing email address** configured for this
repository. Override per commit so the maintainer's global Git identity is
untouched:

```bash
git -c user.name=OpenCode -c user.email=<your-existing-email> \
    commit -m "feat: ..."
```

Do not rewrite published history. Group related files into one commit;
separate docs/backlog-only commits from feature commits. In particular, keep a
**documentation-only** change (`docs:`) in its own commit, separate from
**structural/tooling** changes such as folder scaffolding, `.gitignore`
edits or file moves (`chore:`). That separation is a convention, not a blocker
at this early stage — a `docs:` commit followed by a `chore:` commit is fine.
Do not force-push `main`. Report at the end whether changes were committed and
whether anything was pushed.

## 8. Releases

- Use the `release-cleanup` skill for the full workflow.
- GitHub Releases only: tag `v*.*.*`, attach a built zip, no WP.org SVN.
- Build the zip from the **`cleanup/` plugin folder**, not the repository root.
  Run `composer install --no-dev` first, then stage only the contents of
  `cleanup/`: `cleanup.php`, `includes/`, `LICENSE`, `THIRD-PARTY-NOTICES.md`.
  Never include root docs, `dev/`, `snippets/`, `tests/`, WordPress core,
  `vendor/` or Composer files. Keep `README.md`, `CHANGELOG.md` and
  `SECURITY.md` on GitHub only.
- Keep the plugin header `Version:` in sync with the tag. Do not bump by hand
  outside a release.
- `SECURITY.md` documents how to report a vulnerability privately.

## 9. Updating dependencies

1. `composer outdated`, then update ranges and regenerate `composer.lock`
   with `composer update` — never hand-edit the lockfile.
2. Run `composer check`; CI setup is in `TODO.md`.
3. `composer audit`; fix or document findings.
4. New packages go into `require-dev` only. Adding anything to `require` needs
   the operator's explicit permission first (see §1).
5. Note: WPCS 3.4.1 currently constrains PHPCS to 3.x even though 4.x exists.

## 10. Open decisions

**Decided:** the plugin is licensed under **MIT**. Ship the full MIT license
text as `LICENSE` and use a matching header in shipped PHP files. This is
compatible with the §6 whitelist.

Future decisions and deferred work are in `TODO.md`.

The §6 manifest is the authoritative copy list — use it to avoid copying a
script by mistake or omitting one.

### Operator-granted runtime dependencies

Initially none. If the operator explicitly permits a runtime library, record
it here with package, version, reason and date; otherwise §1 forbids it.

## 11. Before you finish a task

1. `composer check` passes; planned CI checks are mirrored locally.
2. Every new function/class has a DocBlock; every new feature has a registry
   entry and a test from §5 (template and any feature-specific regression
   case).
3. Security review: no secrets, no `die`/`var_dump`, config validated, ABSPATH
   guards present, public exposure policy respected, core APIs used instead of
   custom implementations, generated code original (no copied, translated or
   closely paraphrased code), any copied code on the §6 license whitelist and
   recorded in `THIRD-PARTY-NOTICES.md` with its full license text, and the
   notices file present in the release zip; no runtime Composer package without
   operator permission recorded in §10. Use the `security-review` skill for a
   full audit.
4. No UI, no menu, no saved option, no front-end footprint, no public endpoint.
5. Config/docs reflect reality; `TODO.md` reflects reality; no duplicated or
   stale items.
6. No temp probe/test files left behind; release zip contents still match the
   ship list in §8.
7. `git status` is clean if work was committed; state clearly what was and
   was not pushed.
