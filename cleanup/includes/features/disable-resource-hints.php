<?php

/**
 * Disable core resource hints.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable resource hint removal.
 *
 * Core registers these callbacks in default-filters.php, which is loaded
 * before plugins. Removing the action removes every hint printed through it,
 * including hints added by other code with the wp_resource_hints filter.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function disable_resource_hints(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    remove_action('wp_head', 'wp_resource_hints', 2);
    remove_action('login_head', 'wp_resource_hints', 8);
    remove_action('customize_controls_print_styles', 'wp_resource_hints', 1);
}
