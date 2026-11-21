<?php

/**
 * Remove jQuery from the front end.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable front-end jQuery removal.
 *
 * The wp_enqueue_scripts hook runs only on the front end, so admin jQuery
 * (needed by CMB2 and core) is never touched.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function dequeue_frontend_jquery(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\dequeue_frontend_jquery_scripts', 100);
}

/**
 * Deregister jQuery and jQuery Migrate on the front end.
 *
 * @return void
 */
function dequeue_frontend_jquery_scripts(): void
{
    wp_deregister_script('jquery');
    wp_deregister_script('jquery-migrate');
}
