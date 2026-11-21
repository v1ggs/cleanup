<?php

/**
 * Tests for howdy_text.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\when;

/**
 * Tests for the howdy_text feature.
 */
final class HowdyTextTest extends TestCase
{
    /**
     * The enabled feature replaces the account node with an escaped greeting.
     *
     * @return void
     */
    public function testEnabledFeatureReplacesAccountNode(): void
    {
        $callback = null;

        when('add_action')->alias(
            static function ($hook, $handler) use (&$callback): void {
                self::assertSame('admin_bar_menu', $hook);
                self::assertSame(9992, func_num_args() >= 3 ? func_get_arg(2) : 10);
                $callback = $handler;
            }
        );
        when('wp_get_current_user')->justReturn((object) ['display_name' => 'Ada']);
        when('esc_html')->returnArg();

        \Cleanup\howdy_text('Welcome');

        if (!is_callable($callback)) {
            self::fail('add_action callback was not captured');
        }

        $bar = new \WP_Admin_Bar();
        $callback($bar);

        self::assertSame('Welcome, Ada', $bar->nodes['my-account']['title']);
    }

    /**
     * An empty greeting registers nothing.
     *
     * @return void
     */
    public function testEmptyGreetingRegistersNothing(): void
    {
        $called = false;

        when('add_action')->alias(
            static function () use (&$called): void {
                $called = true;
            }
        );

        \Cleanup\howdy_text('');

        self::assertFalse($called);
    }
}
