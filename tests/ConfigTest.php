<?php

/**
 * Tests for the config loader.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\when;

/**
 * Tests for the config loader.
 */
final class ConfigTest extends TestCase
{
    /**
     * Temporary directories created by the test.
     *
     * @var array<int, string>
     */
    private array $dirs = [];

    /**
     * Remove temporary config files and directories.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        foreach ($this->dirs as $dir) {
            $file = $dir . '/cleanup-config.php';

            if (is_readable($file)) {
                unlink($file);
            }

            if (is_dir($dir)) {
                rmdir($dir);
            }
        }

        $this->dirs = [];
        parent::tearDown();
    }

    /**
     * No config file means every feature stays off.
     *
     * @return void
     */
    public function testNoConfigMeansEverythingOff(): void
    {
        $child = $this->makeDir();
        $parent = $this->makeDir();

        when('get_stylesheet_directory')->justReturn($child);
        when('get_template_directory')->justReturn($parent);

        self::assertSame([], \Cleanup\config());
    }

    /**
     * Theme config values are validated by the per-key schema.
     *
     * @return void
     */
    public function testThemeConfigIsValidatedByType(): void
    {
        $child = $this->makeDir();
        $parent = $this->makeDir();

        file_put_contents(
            $child . '/cleanup-config.php',
            "<?php\nreturn [\n" .
            "  'hide_admin_bar' => true,\n" .
            "  'howdy_text' => 'Welcome',\n" .
            "  'admin_footer_text' => 'Site name',\n" .
            "  'unknown_key' => 'ignored',\n" .
            "  'auto_featured_image' => 'not a bool',\n" .
            "];\n"
        );

        when('get_stylesheet_directory')->justReturn($child);
        when('get_template_directory')->justReturn($parent);

        self::assertSame(
            [
                'hide_admin_bar' => true,
                'howdy_text' => 'Welcome',
                'admin_footer_text' => 'Site name',
            ],
            \Cleanup\config()
        );
    }

    /**
     * Create a unique empty temporary directory.
     *
     * @return string
     */
    private function makeDir(): string
    {
        $dir = sys_get_temp_dir() . '/cleanup-test-' . uniqid();
        mkdir($dir);
        $this->dirs[] = $dir;

        return $dir;
    }
}
