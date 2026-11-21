# Cleanup

A developer-oriented WordPress plugin for custom sites. It changes low-level
WordPress behaviour through a single config file in the active theme.

There is **no admin menu, no settings page, no saved options, no public
endpoints and no front-end output**. With no config file present, the plugin is
completely inert.

> **A polite warning:** these switches change how WordPress behaves. If you are
> not sure what a setting does, leave it out. Nothing here is required for a
> normal site, and removing the key turns the feature off again.

Distributed on **GitHub only** — not in the WordPress.org directory.

## Requirements

- WordPress 7.x
- PHP 8.2+
- Your own local WordPress installation (this project does not use `wp-env`)

In this repository the web root is `wordpress/` and `wp-config.php` lives in
the project root, one level above `ABSPATH`. The project root is the WordPress
plugins directory; the plugin itself is the `cleanup/` folder inside it.

## Install

1. Copy the `cleanup/` folder into your WordPress plugins directory (or point
   `WP_PLUGIN_DIR` at this repository root in the project layout).
2. Activate **Cleanup** in WordPress.
3. Add a `cleanup-config.php` file to the active theme and return a flat array
   of the features you want:

```php
<?php
defined( 'ABSPATH' ) || exit;

return [
    'hide_admin_bar'         => true,
    'howdy_text'             => 'Welcome',
    'admin_footer_text'      => 'Site name',
    'auto_featured_image'    => true,
    'dequeue_frontend_jquery'=> true,
    'disable_customizer'     => true,
    'disable_gutenberg'      => true,
];
```

Config resolution:

- default location: `<theme>/cleanup-config.php`, using
  `get_stylesheet_directory()` so a child theme can override the parent
  (`get_template_directory()` is the fallback);
- `CLEANUP_CONFIG_FILE` overrides the path;
- alternatives: a `CLEANUP_CONFIG` array constant in `wp-config.php`, or a
  `cleanup_defaults` filter from an mu-plugin. The theme file wins if more
  than one source is present;
- unknown keys are ignored; wrong-typed values fall back to defaults; a
  malformed or missing config means everything stays off.

## Features

| Config key                | Value                | What it does                                                        |
| ------------------------- | -------------------- | ------------------------------------------------------------------- |
| `hide_admin_bar`          | `bool`               | Hides the admin bar on the front end                                |
| `howdy_text`              | `string`             | Replaces the default "Howdy" greeting in the admin bar              |
| `admin_footer_text`       | `string` or `array`  | Replaces the admin footer text; array may contain `text` and `url`  |
| `auto_featured_image`     | `bool`               | Sets the first attached image as the featured image on save         |
| `remove_default_taxonomy` | `bool`               | Detaches categories and tags from posts — **new sites only**        |
| `dequeue_frontend_jquery` | `bool`               | Removes front-end jQuery only; admin jQuery stays for CMB2          |
| `disable_customizer`      | `bool`               | Removes the Customizer and blocks direct `customize.php` access     |
| `disable_gutenberg`       | `bool`               | Disables the block editor and removes its assets and global styles  |

`remove-default-taxonomy` changes site structure and can orphan existing
terms. Enable it only before content exists.

## Security

The plugin runs on real client sites, including production. It has no public
attack surface, no front-end fingerprint, and no secrets in tracked files.
Please report vulnerabilities privately using `SECURITY.md`.

## Development

See `DEVELOPMENT.md` for the full rules: security policy, feature manifest,
code style, tests, commits and releases.

```sh
composer install
git config core.hooksPath dev/hooks   # once per clone
composer check
```

Licensed under MIT — see `LICENSE`.
