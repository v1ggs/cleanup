<?php

/**
 * Tests for disable_emojis.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;

/**
 * Tests for the disable_emojis feature.
 */
final class DisableEmojisTest extends TestCase
{
    /**
     * A disabled feature registers and removes nothing.
     *
     * @return void
     */
    public function testDisabledFeatureDoesNothing(): void
    {
        $this->expectNotToPerformAssertions();

        expect('remove_action')->never();
        expect('remove_filter')->never();
        expect('add_action')->never();

        \Cleanup\disable_emojis(false);
    }

    /**
     * The enabled feature removes front-end callbacks and defers admin ones.
     *
     * @return void
     */
    public function testEnabledFeatureRemovesCallbacks(): void
    {
        $this->expectNotToPerformAssertions();

        expect('remove_action')->once()->with('wp_head', 'print_emoji_detection_script', 7);
        expect('remove_action')->once()->with('embed_head', 'print_emoji_detection_script');
        expect('remove_action')->once()->with('wp_print_styles', 'print_emoji_styles');
        expect('remove_filter')->once()->with('the_content_feed', 'wp_staticize_emoji');
        expect('remove_filter')->once()->with('comment_text_rss', 'wp_staticize_emoji');
        expect('remove_filter')->once()->with('wp_mail', 'wp_staticize_emoji_for_email');
        expect('add_action')->once()->with('admin_init', 'Cleanup\\disable_emojis_admin');

        \Cleanup\disable_emojis(true);
    }

    /**
     * The admin worker removes the late-registered callbacks.
     *
     * @return void
     */
    public function testAdminWorkerRemovesCallbacks(): void
    {
        $this->expectNotToPerformAssertions();

        expect('remove_action')->once()->with('admin_print_scripts', 'print_emoji_detection_script');
        expect('remove_action')->once()->with('admin_print_styles', 'print_emoji_styles');

        \Cleanup\disable_emojis_admin();
    }
}
