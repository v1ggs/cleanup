<?php

/**
 * Disable oEmbed discovery on the front end.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable oEmbed discovery removal.
 *
 * Core registers these callbacks in default-filters.php, which is loaded
 * before plugins. Core's earlier discovery callback checks the original hook
 * and stops when that hook is removed, so no commandeering of the action is
 * needed.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function disable_oembed(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
}
