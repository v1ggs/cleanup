<?php

/**
 * Disable emoji scripts, styles and conversion filters.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable emoji removal.
 *
 * The admin callbacks are registered in admin-filters.php, which loads after
 * plugins, so they are removed on admin_init instead of at plugin load.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function disable_emojis(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('embed_head', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_action('admin_init', __NAMESPACE__ . '\\disable_emojis_admin');
}

/**
 * Remove emoji callbacks registered for the admin area.
 *
 * @return void
 */
function disable_emojis_admin(): void
{
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
}
