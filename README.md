# Cleanup

A developer-oriented WordPress plugin for custom websites. It changes low-level
WordPress behaviour through a single config file in the active theme.

There is **no admin menu, no settings page, no saved options, no public
endpoints and no front-end output**. With no config file present, the plugin is
completely inert.

> These switches change how WordPress behaves. If you are not sure what a
> setting does, leave it out. Nothing here is required for a normal website, and
> removing the key turns the feature off again.

Distributed on **GitHub only** — not in the WordPress.org directory.

Built and tested for **classic themes** with the classic editor. Block themes
and Full Site Editing are out of scope and untested; some features should still
work, but the Site Editor is not disabled and Customizer availability differs.

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
    'hide_admin_bar'          => true,
    'howdy_text'              => 'Welcome',
    'admin_footer_text'       => 'Site name',
    'auto_featured_image'     => true,
    'dequeue_frontend_jquery' => true,
    'disable_customizer'      => true,
    'disable_gutenberg'       => true,
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

| Config key                | Value               | What it does                                                                                  |
| ------------------------- | ------------------- | --------------------------------------------------------------------------------------------- |
| `hide_admin_bar`          | `bool`              | Hides the WordPress toolbar on the public website for logged-in users; wp-admin is unaffected |
| `howdy_text`              | `string`            | Replaces the default "Howdy" greeting in the WordPress toolbar                                |
| `admin_footer_text`       | `string` or `array` | Replaces the admin footer text; array may contain `text` and `url`                            |
| `auto_featured_image`     | `bool`              | Sets the first attached image as the featured image on save                                   |
| `dequeue_frontend_jquery` | `bool`              | Removes jQuery from the public website; wp-admin keeps it for other plugins                   |
| `disable_customizer`      | `bool`              | Fully removes the Customizer: core loading, admin UI, direct access and theme-screen links    |
| `disable_gutenberg`       | `bool`              | Disables the block editor and removes its assets and global styles                            |

### `admin_footer_text`

Accepts a string or an array:

```php
// Plain text.
'admin_footer_text' => 'Site name',

// Text with a link. The URL is optional; without it, the text is plain.
'admin_footer_text' => [
    'text' => 'Site name',
    'url'  => 'https://example.com',
],
```

Both values are escaped: `text` with `esc_html()` and `url` with `esc_url()`.
Links open in a new tab with `rel="noopener"`. An empty string or an empty
`text` value leaves the default footer unchanged.

> **Note:** `disable_customizer` stops core from loading the Customizer, removes
> the admin menu item, the admin-bar node, direct `customize.php` access, and
> strips Customize/Live Preview links from the themes screens using core filters
> — no CSS or JS is used to hide remnants. Because the Customizer never loads,
> panels, sections and controls registered by themes or plugins via
> `customize_register` do not run either. Their stored values remain in the
> database, but any code that assumes the Customizer UI exists may need
> adjustment. Full removal is possible in core (it even anticipates plugins
> unhooking all `customize_register` actions).

## Development notes (internal)

Mostly for my own reference. This is a personal project; external pull requests
are not being accepted or reviewed right now.

See `DEVELOPMENT.md` for the full rules: security policy, feature manifest,
code style, tests, commits and releases.

```sh
composer install
git config core.hooksPath dev/hooks   # once per clone
composer check
```

Licensed under MIT — see `LICENSE`.
