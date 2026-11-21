<?php

/**
 * Tests for disable_customizer.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;

/**
 * Tests for the disable_customizer feature.
 */
final class DisableCustomizerTest extends TestCase
{
    /**
     * The enabled feature registers all three removal hooks.
     *
     * @return void
     */
    public function testEnabledFeatureRegistersHooks(): void
    {
        $this->expectNotToPerformAssertions();

        expect('add_action')->once()->with('admin_menu', 'Cleanup\\disable_customizer_menu', 999);
        expect('add_action')->once()->with('admin_bar_menu', 'Cleanup\\disable_customizer_node', 9999);
        expect('add_action')->once()->with('admin_init', 'Cleanup\\disable_customizer_redirect');

        \Cleanup\disable_customizer(true);
    }

    /**
     * The Appearance submenu is removed.
     *
     * @return void
     */
    public function testMenuWorkerRemovesSubmenu(): void
    {
        $this->expectNotToPerformAssertions();

        expect('remove_submenu_page')->once()->with('themes.php', 'customize.php');

        \Cleanup\disable_customizer_menu();
    }

    /**
     * The admin-bar node is removed.
     *
     * @return void
     */
    public function testNodeWorkerRemovesAdminBarNode(): void
    {
        $bar = new \WP_Admin_Bar();
        $bar->add_node(['id' => 'customize']);

        \Cleanup\disable_customizer_node($bar);

        self::assertArrayNotHasKey('customize', $bar->nodes);
    }
}
