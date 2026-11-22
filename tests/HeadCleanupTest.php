<?php

/**
 * Tests for head_cleanup.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;

/**
 * Tests for the head_cleanup feature.
 */
final class HeadCleanupTest extends TestCase
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

        \Cleanup\head_cleanup(false);
    }

    /**
     * The enabled feature removes the core head and header callbacks.
     *
     * @return void
     */
    public function testEnabledFeatureRemovesHeadHooks(): void
    {
        $this->expectNotToPerformAssertions();

        expect('remove_action')->once()->with('wp_head', 'wp_generator');
        expect('remove_action')->once()->with('wp_head', 'wp_shortlink_wp_head');
        expect('remove_action')->once()->with('wp_head', 'rsd_link');
        expect('remove_action')->once()->with('wp_head', 'feed_links', 2);
        expect('remove_action')->once()->with('wp_head', 'feed_links_extra', 3);
        expect('remove_action')->once()->with('wp_head', 'rest_output_link_wp_head');
        expect('remove_action')->once()->with('template_redirect', 'wp_shortlink_header', 11);
        expect('remove_action')->once()->with('template_redirect', 'rest_output_link_header', 11);

        \Cleanup\head_cleanup(true);
    }
}
