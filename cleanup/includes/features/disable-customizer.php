<?php

/**
 * Remove the WordPress Customizer from the admin area.
 *
 * Core map (WP 7.1.5; use symbol names, line numbers drift between releases):
 * - default-filters.php hooks _wp_customize_include on plugins_loaded.
 * - theme.php: _wp_customize_include() creates WP_Customize_Manager.
 * - class-wp-customize-manager.php: the constructor hooks register_controls()
 *   to customize_register; core notes that plugins may unhook all callbacks.
 * - admin-bar.php: wp_admin_bar_customize_menu() adds the 'customize' node.
 * - themes.php: hardcoded Customize buttons; theme data comes from
 *   wp_prepare_themes_for_js() in wp-admin/includes/theme.php.
 * - class-wp-themes-list-table.php: Live Preview link and theme_action_links.
 *
 * Recheck these symbols when core changes; update DEVELOPMENT.md §6 as well.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable the Customizer removal.
 *
 * Stops core from loading the Customizer by unhooking _wp_customize_include
 * from plugins_loaded (see wp-includes/default-filters.php and theme.php),
 * removes the admin UI and redirects direct requests. Also strips Customize
 * links from the themes screens using core filters — no CSS or JS hiding.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function disable_customizer(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    // Prevent WP_Customize_Manager from loading at all on plugins_loaded.
    remove_action('plugins_loaded', '_wp_customize_include');

    add_action('admin_menu', __NAMESPACE__ . '\\disable_customizer_menu', 999);
    add_action('admin_bar_menu', __NAMESPACE__ . '\\disable_customizer_node', 9999);
    add_action('admin_init', __NAMESPACE__ . '\\disable_customizer_redirect');
    add_filter('wp_prepare_themes_for_js', __NAMESPACE__ . '\\disable_customizer_theme_data');
    add_filter('theme_action_links', __NAMESPACE__ . '\\disable_customizer_theme_links');
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
 * Core adds it at priority 40 in wp_admin_bar_customize_menu()
 * (wp-includes/admin-bar.php; hooked in class-wp-admin-bar.php).
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

/**
 * Remove the Customize action from theme data used by themes.php.
 *
 * Core adds $theme['actions']['customize'] in wp_prepare_themes_for_js()
 * (wp-admin/includes/theme.php). themes.php uses it for the active-theme card
 * and its JS templates; removing it strips those buttons without CSS or JS.
 *
 * @param array<string, mixed> $themes Prepared theme data.
 * @return array<string, mixed>
 */
function disable_customizer_theme_data(array $themes): array
{
    foreach ($themes as $slug => $theme) {
        if (!isset($theme['actions']) || !is_array($theme['actions'])) {
            continue;
        }

        unset($themes[$slug]['actions']['customize']);
    }

    return $themes;
}

/**
 * Remove Live Preview from the theme list table.
 *
 * Core adds the link in WP_Themes_List_Table::display_rows()
 * (wp-admin/includes/class-wp-themes-list-table.php) and exposes theme_action_links
 * there. Removing the 'preview' action strips the link.
 *
 * @param array<string, string> $actions Theme row action links.
 * @return array<string, string>
 */
function disable_customizer_theme_links(array $actions): array
{
    unset($actions['preview']);

    return $actions;
}
