<?php

/**
 * Disable the block editor and remove its front-end and admin styles.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable the block editor removal.
 *
 * Also removes generated global styles, so no separate config key is needed
 * for that behaviour.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function disable_gutenberg(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    add_filter('use_block_editor_for_post', '__return_false', 100);
    add_action('after_setup_theme', __NAMESPACE__ . '\\disable_gutenberg_global_styles', 20);
    add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\disable_gutenberg_assets', 100);
    add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\disable_gutenberg_assets', 100);
}

/**
 * Stop core from outputting generated global styles on the front end.
 *
 * @return void
 */
function disable_gutenberg_global_styles(): void
{
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
}

/**
 * Dequeue block-library and global-styles stylesheets.
 *
 * @return void
 */
function disable_gutenberg_assets(): void
{
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
    wp_dequeue_style('core-block-supports');
    wp_dequeue_style('classic-theme-styles');
}
