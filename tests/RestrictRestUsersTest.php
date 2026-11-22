<?php

/**
 * Tests for restrict_rest_users.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;
use function Brain\Monkey\Functions\when;

/**
 * Tests for the restrict_rest_users feature.
 */
final class RestrictRestUsersTest extends TestCase
{
    /**
     * A disabled feature registers no filter.
     *
     * @return void
     */
    public function testDisabledFeatureRegistersNothing(): void
    {
        $this->expectNotToPerformAssertions();

        expect('add_filter')->never();

        \Cleanup\restrict_rest_users(false);
    }

    /**
     * The enabled feature registers the endpoint filter.
     *
     * @return void
     */
    public function testEnabledFeatureRegistersFilter(): void
    {
        $this->expectNotToPerformAssertions();

        expect('add_filter')->once()->with('rest_endpoints', 'Cleanup\\restrict_rest_user_endpoints');

        \Cleanup\restrict_rest_users(true);
    }

    /**
     * Logged-out requests lose the enumeration routes.
     *
     * @return void
     */
    public function testWorkerRemovesRoutesForLoggedOutRequests(): void
    {
        when('is_user_logged_in')->justReturn(false);

        $endpoints = [
            '/wp/v2/users' => [],
            '/wp/v2/users/(?P<id>[\d]+)' => [],
            '/wp/v2/users/me' => [],
            '/wp/v2/posts' => [],
        ];

        $result = \Cleanup\restrict_rest_user_endpoints($endpoints);

        self::assertArrayNotHasKey('/wp/v2/users', $result);
        self::assertArrayNotHasKey('/wp/v2/users/(?P<id>[\d]+)', $result);
        self::assertArrayHasKey('/wp/v2/users/me', $result);
        self::assertArrayHasKey('/wp/v2/posts', $result);
    }

    /**
     * Logged-in requests keep every route.
     *
     * @return void
     */
    public function testWorkerKeepsRoutesForLoggedInRequests(): void
    {
        when('is_user_logged_in')->justReturn(true);

        $endpoints = [
            '/wp/v2/users' => [],
            '/wp/v2/users/(?P<id>[\d]+)' => [],
        ];

        self::assertSame($endpoints, \Cleanup\restrict_rest_user_endpoints($endpoints));
    }
}
