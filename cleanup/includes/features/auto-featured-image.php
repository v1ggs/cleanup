<?php

/**
 * Set the first attached image as a post's featured image.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Enable automatic featured images.
 *
 * @param bool $enabled Whether the feature is enabled.
 * @return void
 */
function auto_featured_image(bool $enabled): void
{
    if (!$enabled) {
        return;
    }

    add_action('save_post', __NAMESPACE__ . '\\auto_featured_image_set');
}

/**
 * Assign the first attached image when no featured image is set.
 *
 * Runs on save_post but skips autosaves, revisions and posts that already
 * have a thumbnail. Uses only core query and post-thumbnail functions.
 *
 * @param int $post_id Saved post ID.
 * @return void
 */
function auto_featured_image_set(int $post_id): void
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }

    if (get_post_thumbnail_id($post_id)) {
        return;
    }

    $images = get_posts(
        [
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'post_parent' => $post_id,
            'post_mime_type' => 'image',
            'numberposts' => 1,
            'order' => 'ASC',
            'orderby' => 'menu_order',
            'fields' => 'ids',
        ]
    );

    if (empty($images)) {
        return;
    }

    set_post_thumbnail($post_id, (int) $images[0]);
}
