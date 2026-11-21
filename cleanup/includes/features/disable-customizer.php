<?php

/**
 * Remove the WordPress Customizer from the admin area.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable the Customizer removal.
 *
 * Uses three core hooks: remove the Appearance submenu, remove the admin-bar
 * node late, and redirect direct requests to customize.php.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function disable_customizer(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    add_action('admin_menu', __NAMESPACE__ . '\\disable_customizer_menu', 999);
    add_action('admin_bar_menu', __NAMESPACE__ . '\\disable_customizer_node', 9999);
    add_action('admin_init', __NAMESPACE__ . '\\disable_customizer_redirect');
}

/**
 * Remove Appearance > Customize.
 *
 * @return void
 */
function disable_customizer_menu(): void
{
    remove_submenu_page('themes.php', 'customize.php');
}

/**
 * Remove the Customize node from the admin bar.
 *
 * Runs late because other plugins may re-add the node via customize_register.
 *
 * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
 * @return void
 */
function disable_customizer_node(\WP_Admin_Bar $wp_admin_bar): void
{
    $wp_admin_bar->remove_node('customize');
}

/**
 * Redirect direct requests to customize.php back to the home page.
 *
 * @return void
 */
function disable_customizer_redirect(): void
{
    global $pagenow;

    if ('customize.php' === $pagenow) {
        wp_safe_redirect(home_url('/'));
        exit;
    }
}
