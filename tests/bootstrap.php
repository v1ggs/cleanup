<?php

/**
 * PHPUnit bootstrap for the Cleanup plugin tests.
 *
 * @package Cleanup
 */

require dirname(__DIR__) . '/vendor/autoload.php';

if (!defined('ABSPATH')) {
    define('ABSPATH', sys_get_temp_dir() . '/');
}

if (!class_exists('WP_Admin_Bar')) {
    /**
     * Minimal admin bar double used by feature tests.
     */
    class WP_Admin_Bar
    {
        /**
         * Registered nodes.
         *
         * @var array<string, array<string, mixed>>
         */
        public array $nodes = [];

        /**
         * Add a node.
         *
         * @param array<string, mixed> $args Node arguments.
         * @return void
         */
        public function add_node(array $args): void
        {
            $id = $args['id'] ?? '';
            $this->nodes[ $id ] = $args;
        }

        /**
         * Remove a node.
         *
         * @param string $id Node ID.
         * @return void
         */
        public function remove_node(string $id): void
        {
            unset($this->nodes[ $id ]);
        }
    }
}

require_once dirname(__DIR__) . '/cleanup/includes/config.php';
require_once dirname(__DIR__) . '/cleanup/includes/features.php';
require_once __DIR__ . '/TestCase.php';
