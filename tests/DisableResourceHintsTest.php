<?php

/**
 * Tests for disable_resource_hints.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;

/**
 * Tests for the disable_resource_hints feature.
 */
final class DisableResourceHintsTest extends TestCase
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

        \Cleanup\disable_resource_hints(false);
    }

    /**
     * The enabled feature removes all three core callbacks.
     *
     * @return void
     */
    public function testEnabledFeatureRemovesAllCallbacks(): void
    {
        $this->expectNotToPerformAssertions();

        expect('remove_action')->once()->with('wp_head', 'wp_resource_hints', 2);
        expect('remove_action')->once()->with('login_head', 'wp_resource_hints', 8);
        expect('remove_action')->once()->with('customize_controls_print_styles', 'wp_resource_hints', 1);

        \Cleanup\disable_resource_hints(true);
    }
}
