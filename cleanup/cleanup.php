<?php

/**
 * Plugin Name:       Cleanup
 * Plugin URI:        https://github.com/v1ggs/cleanup
 * Description:       Config-gated WordPress cleanup for custom sites.
 * Version:           1.0.0
 * Author:            v1ggs
 * Author URI:        https://github.com/v1ggs
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Requires at least: 7.0
 * Requires PHP:      8.2
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/features.php';

/**
 * Attach the hooks for every enabled feature.
 *
 * Runs at plugin file load, before the plugins_loaded action fires, so
 * features may hook plugins_loaded, after_setup_theme, init and similar.
 *
 * @return void
 */
function bootstrap(): void
{
    $config = config();

    foreach (features() as $key => $feature) {
        if (empty($config[ $key ]) || ! is_callable($feature['callback'])) {
            continue;
        }

        $feature['callback']($config[ $key ]);
    }
}

bootstrap();
