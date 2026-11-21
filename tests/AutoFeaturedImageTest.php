<?php

/**
 * Tests for auto_featured_image.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\Functions\when;

/**
 * Tests for the auto_featured_image feature.
 */
final class AutoFeaturedImageTest extends TestCase
{
    /**
     * The enabled feature registers the save_post action.
     *
     * @return void
     */
    public function testEnabledFeatureRegistersSavePost(): void
    {
        $this->expectNotToPerformAssertions();

        expect('add_action')
            ->once()
            ->with('save_post', 'Cleanup\\auto_featured_image_set');

        \Cleanup\auto_featured_image(true);
    }

    /**
     * The first attached image becomes the featured image.
     *
     * @return void
     */
    public function testWorkerSetsFirstAttachedImage(): void
    {
        $this->expectNotToPerformAssertions();

        when('wp_is_post_revision')->justReturn(false);
        when('wp_is_post_autosave')->justReturn(false);
        when('get_post_thumbnail_id')->justReturn(0);
        when('get_posts')->justReturn([42]);
        expect('set_post_thumbnail')->once()->with(7, 42);

        \Cleanup\auto_featured_image_set(7);
    }

    /**
     * An existing featured image is never overwritten.
     *
     * @return void
     */
    public function testWorkerSkipsExistingThumbnail(): void
    {
        $this->expectNotToPerformAssertions();

        when('wp_is_post_revision')->justReturn(false);
        when('wp_is_post_autosave')->justReturn(false);
        when('get_post_thumbnail_id')->justReturn(9);
        expect('set_post_thumbnail')->never();

        \Cleanup\auto_featured_image_set(7);
    }
}
