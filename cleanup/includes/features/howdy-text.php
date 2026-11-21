<?php

/**
 * Replace the default "Howdy" greeting in the admin bar.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Replace the admin bar account label with a custom greeting.
 *
 * The configured text is prepended to the current user's display name. Both
 * parts are escaped before being passed to the admin bar.
 *
 * @param string $greeting Configured greeting, e.g. "Welcome".
 * @return void
 */
function howdy_text(string $greeting): void
{
    if ('' === $greeting) {
        return;
    }

    add_action(
        'admin_bar_menu',
        static function (\WP_Admin_Bar $wp_admin_bar) use ($greeting): void {
            $user = wp_get_current_user();

            $wp_admin_bar->add_node(
                [
                    'id' => 'my-account',
                    'title' => sprintf(
                        '%s, %s',
                        esc_html($greeting),
                        esc_html($user->display_name)
                    ),
                ]
            );
        },
        9992
    );
}
