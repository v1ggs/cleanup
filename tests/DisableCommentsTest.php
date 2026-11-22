<?php

/**
 * Tests for disable_comments.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;

/**
 * Tests for the disable_comments feature.
 */
final class DisableCommentsTest extends TestCase
{
    /**
     * A disabled feature registers no filters.
     *
     * @return void
     */
    public function testDisabledFeatureRegistersNothing(): void
    {
        $this->expectNotToPerformAssertions();

        expect('add_filter')->never();

        \Cleanup\disable_comments(false);
    }

    /**
     * The enabled feature registers both core filters.
     *
     * @return void
     */
    public function testEnabledFeatureRegistersFilters(): void
    {
        $this->expectNotToPerformAssertions();

        expect('add_filter')->once()->with('comments_open', '__return_false');
        expect('add_filter')->once()->with('pings_open', '__return_false');

        \Cleanup\disable_comments(true);
    }
}
