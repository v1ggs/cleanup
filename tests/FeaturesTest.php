<?php

/**
 * Tests for the feature registry.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

/**
 * Tests for the feature registry.
 */
final class FeaturesTest extends TestCase
{
    /**
     * The registry exposes the expected keys with callable callbacks.
     *
     * @return void
     */
    public function testRegistryHasExpectedKeysAndCallbacks(): void
    {
        $expected = [
            'hide_admin_bar',
            'howdy_text',
            'admin_footer_text',
            'auto_featured_image',
            'dequeue_frontend_jquery',
            'disable_customizer',
            'disable_gutenberg',
            'disable_xmlrpc',
            'restrict_rest_users',
            'head_cleanup',
            'disable_oembed',
            'disable_resource_hints',
            'disable_emojis',
        ];

        $features = \Cleanup\features();

        self::assertSame($expected, array_keys($features));

        foreach ($features as $key => $feature) {
            self::assertContains($feature['type'], ['bool', 'string', 'array', 'string|array']);
            self::assertIsCallable($feature['callback'], $key);
        }
    }
}
