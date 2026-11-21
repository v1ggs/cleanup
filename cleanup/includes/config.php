<?php

/**
 * Configuration loader for the Cleanup plugin.
 *
 * The theme config file is the only control surface. With no config, the
 * plugin is inert.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Return the validated configuration.
 *
 * The first source wins: theme config file, CLEANUP_CONFIG constant, then the
 * cleanup_defaults filter. Unknown keys and wrong-typed values are ignored.
 *
 * @return array<string, bool|string|array<string, string>>
 */
function config(): array
{
    static $config = null;

    if (null !== $config) {
        return $config;
    }

    $config = [];
    $source = load_config_source();

    foreach ($source as $key => $value) {
        $schema = features()[ $key ] ?? null;

        if (null === $schema) {
            continue;
        }

        if ('bool' === $schema['type'] && is_bool($value)) {
            $config[ $key ] = $value;
        } elseif ('string' === $schema['type'] && is_string($value)) {
            $config[ $key ] = $value;
        } elseif ('string|array' === $schema['type'] && is_string($value)) {
            $config[ $key ] = $value;
        } elseif ('string|array' === $schema['type'] && is_array($value)) {
            $config[ $key ] = sanitize_config_array($value);
        }
    }

    return $config;
}

/**
 * Load the raw configuration from the first available source.
 *
 * @return array<string, mixed>
 */
function load_config_source(): array
{
    $path = config_path();

    if ('' !== $path) {
        try {
            $loaded = require $path;

            if (is_array($loaded)) {
                return $loaded;
            }
        } catch (\Throwable $error) {
            // A broken config file must not take down the site.
            return [];
        }
    }

    if (defined('CLEANUP_CONFIG') && is_array(CLEANUP_CONFIG)) {
        return CLEANUP_CONFIG;
    }

    $filtered = apply_filters('cleanup_defaults', []);

    return is_array($filtered) ? $filtered : [];
}

/**
 * Resolve the theme config file path.
 *
 * Never derives a path from request data. Returns an empty string when no
 * config file is present.
 *
 * @return string
 */
function config_path(): string
{
    if (defined('CLEANUP_CONFIG_FILE') && is_string(CLEANUP_CONFIG_FILE)) {
        return is_readable(CLEANUP_CONFIG_FILE) ? CLEANUP_CONFIG_FILE : '';
    }

    $child = get_stylesheet_directory() . '/cleanup-config.php';

    if (is_readable($child)) {
        return $child;
    }

    $parent = get_template_directory() . '/cleanup-config.php';

    return is_readable($parent) ? $parent : '';
}

/**
 * Allow only string keys and values in array-valued config entries.
 *
 * @param array<mixed, mixed> $value Raw config value.
 * @return array<string, string>
 */
function sanitize_config_array(array $value): array
{
    $clean = [];

    foreach ($value as $key => $item) {
        if (is_string($key) && is_string($item)) {
            $clean[ $key ] = $item;
        }
    }

    return $clean;
}
