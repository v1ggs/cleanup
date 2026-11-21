<?php

/**
 * Hide the front-end admin bar.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Hide the WordPress admin bar on the front end.
 *
 * Uses the core show_admin_bar filter with the core __return_false callback.
 * The admin bar in wp-admin is unaffected.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function hide_admin_bar(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    add_filter('show_admin_bar', '__return_false');
}
