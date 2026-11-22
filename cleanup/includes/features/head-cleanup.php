<?php

/**
 * Remove generated metadata from the public head and HTTP headers.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable head metadata removal.
 *
 * Core registers these callbacks in default-filters.php, which is loaded
 * before plugins, so they can be removed when the plugin loads.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function head_cleanup(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('template_redirect', 'wp_shortlink_header', 11);
    remove_action('template_redirect', 'rest_output_link_header', 11);
}
