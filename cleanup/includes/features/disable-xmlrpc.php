<?php

/**
 * Disable XML-RPC.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable XML-RPC removal.
 *
 * Uses the core filters that WP itself provides for this purpose, so no
 * request rewriting or endpoint registration is involved.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function disable_xmlrpc(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    add_filter('xmlrpc_enabled', '__return_false');
    add_filter('xmlrpc_methods', '__return_empty_array');
}
