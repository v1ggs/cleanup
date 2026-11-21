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
     * The enabled feature stops core loading and registers removal hooks.
     *
     * @return void
     */
    public function testEnabledFeatureRegistersHooks(): void
    {
        $this->expectNotToPerformAssertions();

        expect('remove_action')->once()->with('plugins_loaded', '_wp_customize_include');
        expect('add_action')->once()->with('admin_menu', 'Cleanup\\disable_customizer_menu', 999);
        expect('add_action')->once()->with('admin_bar_menu', 'Cleanup\\disable_customizer_node', 9999);
        expect('add_action')->once()->with('admin_init', 'Cleanup\\disable_customizer_redirect');
        expect('add_filter')->once()->with('wp_prepare_themes_for_js', 'Cleanup\\disable_customizer_theme_data');
        expect('add_filter')->once()->with('theme_action_links', 'Cleanup\\disable_customizer_theme_links');

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

    /**
     * The Customize action is stripped from theme data.
     *
     * @return void
     */
    public function testThemeDataStripsCustomizeAction(): void
    {
        $themes = [
            'twentytwentyfive' => [
                'actions' => [
                    'customize' => 'customize.php?theme=twentytwentyfive',
                    'activate' => 'themes.php?action=activate',
                ],
            ],
        ];

        $result = \Cleanup\disable_customizer_theme_data($themes);

        self::assertArrayNotHasKey('customize', $result['twentytwentyfive']['actions']);
        self::assertArrayHasKey('activate', $result['twentytwentyfive']['actions']);
    }

    /**
     * Live Preview is stripped from theme row links.
     *
     * @return void
     */
    public function testThemeLinksStripLivePreview(): void
    {
        $actions = [
            'preview' => '<a href="customize.php">Live Preview</a>',
            'activate' => '<a href="themes.php?action=activate">Activate</a>',
        ];

        $result = \Cleanup\disable_customizer_theme_links($actions);

        self::assertArrayNotHasKey('preview', $result);
        self::assertArrayHasKey('activate', $result);
    }
}
