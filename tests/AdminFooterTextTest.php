<?php

/**
 * Tests for admin_footer_text.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\when;

/**
 * Tests for the admin_footer_text feature.
 */
final class AdminFooterTextTest extends TestCase
{
    /**
     * A string value renders as escaped plain text.
     *
     * @return void
     */
    public function testStringValueRendersPlainText(): void
    {
        $callback = null;

        when('add_filter')->alias(
            static function ($hook, $handler) use (&$callback): void {
                self::assertSame('admin_footer_text', $hook);
                $callback = $handler;
            }
        );
        when('esc_html')->returnArg();

        \Cleanup\admin_footer_text('Site name');

        if (!is_callable($callback)) {
            self::fail('add_filter callback was not captured');
        }

        self::assertSame('Site name', $callback());
    }

    /**
     * An array value with a URL renders as a safe link.
     *
     * @return void
     */
    public function testArrayValueRendersLink(): void
    {
        $callback = null;

        when('add_filter')->alias(
            static function ($hook, $handler) use (&$callback): void {
                self::assertSame('admin_footer_text', $hook);
                $callback = $handler;
            }
        );
        when('esc_html')->returnArg();
        when('esc_url')->returnArg();

        \Cleanup\admin_footer_text(
            [
                'text' => 'Site name',
                'url' => 'https://example.com',
            ]
        );

        if (!is_callable($callback)) {
            self::fail('add_filter callback was not captured');
        }

        self::assertSame(
            '<a href="https://example.com" target="_blank" rel="noopener">Site name</a>',
            $callback()
        );
    }
}
