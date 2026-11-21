<?php

/**
 * Replace the default WordPress admin footer text.
 *
 * @package Cleanup
 */

namespace Cleanup;

defined('ABSPATH') || exit;

/**
 * Replace the admin footer text with a configured label or link.
 *
 * A string renders as plain text. An array may contain "text" and an optional
 * "url"; when a URL is present the text becomes a link.
 *
 * @param string|array<string, string> $value Configured footer value.
 * @return void
 */
function admin_footer_text(string|array $value): void
{
    if (is_string($value)) {
        $text = $value;
        $url = '';
    } else {
        $text = $value['text'] ?? '';
        $url = $value['url'] ?? '';
    }

    if ('' === $text) {
        return;
    }

    add_filter(
        'admin_footer_text',
        static function () use ($text, $url): string {
            $label = esc_html($text);

            if ('' === $url) {
                return $label;
            }

            return sprintf(
                '<a href="%s" target="_blank" rel="noopener">%s</a>',
                esc_url($url),
                $label
            );
        }
    );
}
