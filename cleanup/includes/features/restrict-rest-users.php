<?php

/**
 * Restrict public user endpoints in the REST API.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable the REST user endpoint restriction.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function restrict_rest_users(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    add_filter('rest_endpoints', __NAMESPACE__ . '\\restrict_rest_user_endpoints');
}

/**
 * Remove user enumeration routes from logged-out REST requests.
 *
 * The current-user route is kept: it only ever returns data for the caller.
 *
 * @param array<string, mixed> $endpoints Registered REST endpoints.
 * @return array<string, mixed>
 */
function restrict_rest_user_endpoints(array $endpoints): array
{
    if (is_user_logged_in()) {
        return $endpoints;
    }

    unset(
        $endpoints['/wp/v2/users'],
        $endpoints['/wp/v2/users/(?P<id>[\d]+)']
    );

    return $endpoints;
}
