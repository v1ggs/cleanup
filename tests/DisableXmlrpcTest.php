<?php

/**
 * Tests for disable_xmlrpc.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;

/**
 * Tests for the disable_xmlrpc feature.
 */
final class DisableXmlrpcTest extends TestCase
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

        \Cleanup\disable_xmlrpc(false);
    }

    /**
     * The enabled feature registers both core filters.
     *
     * @return void
     */
    public function testEnabledFeatureRegistersFilters(): void
    {
        $this->expectNotToPerformAssertions();

        expect('add_filter')->once()->with('xmlrpc_enabled', '__return_false');
        expect('add_filter')->once()->with('xmlrpc_methods', '__return_empty_array');

        \Cleanup\disable_xmlrpc(true);
    }
}
