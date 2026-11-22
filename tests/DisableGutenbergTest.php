<?php

/**
 * Tests for disable_gutenberg.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;

/**
 * Tests for the disable_gutenberg feature.
 */
final class DisableGutenbergTest extends TestCase
{
    /**
     * The enabled feature registers the editor and asset hooks.
     *
     * @return void
     */
    public function testEnabledFeatureRegistersHooks(): void
    {
        $this->expectNotToPerformAssertions();

        expect('add_filter')->once()->with('use_block_editor_for_post', '__return_false', 100);
        expect('add_action')->once()->with('after_setup_theme', 'Cleanup\\disable_gutenberg_global_styles', 20);
        expect('add_action')->once()->with('wp_enqueue_scripts', 'Cleanup\\disable_gutenberg_assets', 100);
        expect('add_action')->once()->with('admin_enqueue_scripts', 'Cleanup\\disable_gutenberg_assets', 100);

        \Cleanup\disable_gutenberg(true);
    }

    /**
     * Global style actions are removed.
     *
     * @return void
     */
    public function testGlobalStylesWorkerRemovesActions(): void
    {
        $this->expectNotToPerformAssertions();

        expect('remove_action')->once()->with('wp_enqueue_scripts', 'wp_enqueue_global_styles');
        expect('remove_action')->once()->with('wp_footer', 'wp_enqueue_global_styles', 1);

        \Cleanup\disable_gutenberg_global_styles();
    }

    /**
     * Block styles are dequeued.
     *
     * @return void
     */
    public function testAssetsWorkerDequeuesStyles(): void
    {
        $this->expectNotToPerformAssertions();

        expect('wp_dequeue_style')->once()->with('wp-block-library');
        expect('wp_dequeue_style')->once()->with('wp-block-library-theme');
        expect('wp_dequeue_style')->once()->with('global-styles');
        expect('wp_dequeue_style')->once()->with('core-block-supports');
        expect('wp_dequeue_style')->once()->with('classic-theme-styles');

        \Cleanup\disable_gutenberg_assets();
    }
}
