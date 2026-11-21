<?php

/**
 * Detach the default category and tag taxonomies from posts.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable removal of the default post taxonomies.
 *
 * This feature is for new sites only: detaching the taxonomies can orphan
 * existing terms. It does not unregister the taxonomies globally.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function remove_default_taxonomy(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    add_action('init', __NAMESPACE__ . '\\remove_default_taxonomy_detach', 100);
}

/**
 * Detach category and post_tag from the post post type.
 *
 * Runs late on init so core has already registered the taxonomies.
 *
 * @return void
 */
function remove_default_taxonomy_detach(): void
{
    unregister_taxonomy_for_object_type('category', 'post');
    unregister_taxonomy_for_object_type('post_tag', 'post');
}
