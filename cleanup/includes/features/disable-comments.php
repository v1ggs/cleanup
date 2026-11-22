<?php

/**
 * Disable new comments and pings.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable comment and ping removal.
 *
 * These core filters close new discussion on every post. Comments already
 * stored in the database are not changed.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function disable_comments(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    add_filter('comments_open', '__return_false');
    add_filter('pings_open', '__return_false');
}
