<?php

/**
 * Tests for remove_default_taxonomy.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;

/**
 * Tests for the remove_default_taxonomy feature.
 */
final class RemoveDefaultTaxonomyTest extends TestCase
{
    /**
     * The enabled feature registers the late init hook.
     *
     * @return void
     */
    public function testEnabledFeatureRegistersInitHook(): void
    {
        $this->expectNotToPerformAssertions();

        expect('add_action')
            ->once()
            ->with('init', 'Cleanup\\remove_default_taxonomy_detach', 100);

        \Cleanup\remove_default_taxonomy(true);
    }

    /**
     * The worker detaches both default taxonomies from posts.
     *
     * @return void
     */
    public function testWorkerDetachesTaxonomies(): void
    {
        $this->expectNotToPerformAssertions();

        expect('unregister_taxonomy_for_object_type')->once()->with('category', 'post');
        expect('unregister_taxonomy_for_object_type')->once()->with('post_tag', 'post');

        \Cleanup\remove_default_taxonomy_detach();
    }
}
