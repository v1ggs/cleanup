<?php

/**
 * Feature registry for the Cleanup plugin.
 *
 * Each entry declares the config value type and the callback that attaches
 * the feature's hooks. A null callback means the feature has not been ported
 * from snippets/ yet; such entries only define the config schema.
 *
 * The disable_gutenberg feature also removes generated global styles; there
 * is no separate dequeue_global_styles config key.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

require_once __DIR__ . '/features/hide-admin-bar.php';
require_once __DIR__ . '/features/howdy-text.php';
require_once __DIR__ . '/features/admin-footer-text.php';
require_once __DIR__ . '/features/auto-featured-image.php';
require_once __DIR__ . '/features/dequeue-frontend-jquery.php';
require_once __DIR__ . '/features/disable-customizer.php';
require_once __DIR__ . '/features/disable-gutenberg.php';
require_once __DIR__ . '/features/disable-xmlrpc.php';
require_once __DIR__ . '/features/restrict-rest-users.php';
require_once __DIR__ . '/features/head-cleanup.php';
require_once __DIR__ . '/features/disable-oembed.php';
require_once __DIR__ . '/features/disable-resource-hints.php';
require_once __DIR__ . '/features/disable-emojis.php';
require_once __DIR__ . '/features/disable-comments.php';

/**
 * Return the feature registry.
 *
 * @return array<string, array{type: string, callback: callable|null}>
 */
function features(): array
{
    return [
        'hide_admin_bar' => [
            'type' => 'bool',
            'callback' => __NAMESPACE__ . '\\hide_admin_bar',
        ],
        'howdy_text' => [
            'type' => 'string',
            'callback' => __NAMESPACE__ . '\\howdy_text',
        ],
        'admin_footer_text' => [
            'type' => 'string|array',
            'callback' => __NAMESPACE__ . '\\admin_footer_text',
        ],
        'auto_featured_image' => [
            'type' => 'bool',
            'callback' => __NAMESPACE__ . '\\auto_featured_image',
        ],
        'dequeue_frontend_jquery' => [
            'type' => 'bool',
            'callback' => __NAMESPACE__ . '\\dequeue_frontend_jquery',
        ],
        'disable_customizer' => [
            'type' => 'bool',
            'callback' => __NAMESPACE__ . '\\disable_customizer',
        ],
        'disable_gutenberg' => [
            'type' => 'bool',
            'callback' => __NAMESPACE__ . '\\disable_gutenberg',
        ],
        'disable_xmlrpc' => [
            'type' => 'bool',
            'callback' => __NAMESPACE__ . '\\disable_xmlrpc',
        ],
        'restrict_rest_users' => [
            'type' => 'bool',
            'callback' => __NAMESPACE__ . '\\restrict_rest_users',
        ],
        'head_cleanup' => [
            'type' => 'bool',
            'callback' => __NAMESPACE__ . '\\head_cleanup',
        ],
        'disable_oembed' => [
            'type' => 'bool',
            'callback' => __NAMESPACE__ . '\\disable_oembed',
        ],
        'disable_resource_hints' => [
            'type' => 'bool',
            'callback' => __NAMESPACE__ . '\\disable_resource_hints',
        ],
        'disable_emojis' => [
            'type' => 'bool',
            'callback' => __NAMESPACE__ . '\\disable_emojis',
        ],
        'disable_comments' => [
            'type' => 'bool',
            'callback' => __NAMESPACE__ . '\\disable_comments',
        ],
    ];
}
