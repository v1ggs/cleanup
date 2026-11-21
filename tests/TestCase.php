<?php

/**
 * Base test case with Brain Monkey setup.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use Brain\Monkey;

/**
 * Shared PHPUnit base class for plugin tests.
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Start Brain Monkey before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    /**
     * Stop Brain Monkey after each test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}
