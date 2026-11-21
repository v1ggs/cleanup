<?php

/**
 * Tests for hide_admin_bar.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;

/**
 * Tests for the hide_admin_bar feature.
 */
final class HideAdminBarTest extends TestCase
{
    /**
     * The enabled feature registers the core filter.
     *
     * @return void
     */
    public function testEnabledFeatureAddsCoreFilter(): void
    {
        $this->expectNotToPerformAssertions();

        expect('add_filter')
            ->once()
            ->with('show_admin_bar', '__return_false');

        \Cleanup\hide_admin_bar(true);
    }

    /**
     * The disabled feature registers nothing.
     *
     * @return void
     */
    public function testDisabledFeatureAddsNoFilter(): void
    {
        $this->expectNotToPerformAssertions();

        expect('add_filter')->never();

        \Cleanup\hide_admin_bar(false);
    }
}
