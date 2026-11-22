<?php

/**
 * Tests for disable_oembed.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;

/**
 * Tests for the disable_oembed feature.
 */
final class DisableOembedTest extends TestCase
{
    /**
     * A disabled feature removes nothing.
     *
     * @return void
     */
    public function testDisabledFeatureRemovesNothing(): void
    {
        $this->expectNotToPerformAssertions();

        expect('remove_action')->never();

        \Cleanup\disable_oembed(false);
    }

    /**
     * The enabled feature removes both head callbacks.
     *
     * @return void
     */
    public function testEnabledFeatureRemovesHeadCallbacks(): void
    {
        $this->expectNotToPerformAssertions();

        expect('remove_action')->once()->with('wp_head', 'wp_oembed_add_discovery_links');
        expect('remove_action')->once()->with('wp_head', 'wp_oembed_add_host_js');

        \Cleanup\disable_oembed(true);
    }
}
