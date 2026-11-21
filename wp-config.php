<?php

/**
 * WordPress configuration for this project.
 *
 * Lives one level above the web root (wordpress/), where WordPress finds it
 * automatically. It is tracked in git, so it must contain no real credentials:
 * database settings, salts and the site URL come from environment variables.
 *
 * @package Cleanup
 */

defined('ABSPATH') || exit;

// ** Database settings ** //
define('DB_NAME', getenv('DB_NAME') ?: 'cleanup');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// ** Authentication keys and salts ** //
define('AUTH_KEY', getenv('AUTH_KEY') ?: 'change-me-auth-key');
define('SECURE_AUTH_KEY', getenv('SECURE_AUTH_KEY') ?: 'change-me-secure-auth-key');
define('LOGGED_IN_KEY', getenv('LOGGED_IN_KEY') ?: 'change-me-logged-in-key');
define('NONCE_KEY', getenv('NONCE_KEY') ?: 'change-me-nonce-key');
define('AUTH_SALT', getenv('AUTH_SALT') ?: 'change-me-auth-salt');
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT') ?: 'change-me-secure-auth-salt');
define('LOGGED_IN_SALT', getenv('LOGGED_IN_SALT') ?: 'change-me-logged-in-salt');
define('NONCE_SALT', getenv('NONCE_SALT') ?: 'change-me-nonce-salt');

/**
 * WordPress database table prefix.
 *
 * @var string
 */
$table_prefix = getenv('WP_TABLE_PREFIX') ?: 'wp_';

// ** Debugging ** //
define('WP_DEBUG', filter_var(getenv('WP_DEBUG'), FILTER_VALIDATE_BOOLEAN));

// ** Project layout ** //
// The project root is the WordPress plugins directory; plugins load by path.
define('WP_PLUGIN_DIR', __DIR__);
define('WP_PLUGIN_URL', getenv('WP_PLUGIN_URL'));

/* That's all, stop editing! Happy publishing. */

require_once ABSPATH . 'wp-settings.php';
