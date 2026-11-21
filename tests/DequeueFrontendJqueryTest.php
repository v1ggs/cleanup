<?php

/**
 * Tests for dequeue_frontend_jquery.
 *
 * @package Cleanup
 */

namespace Cleanup\Tests;

use function Brain\Monkey\Functions\expect;

/**
 * Tests for the dequeue_frontend_jquery feature.
 */
final class DequeueFrontendJqueryTest extends TestCase
{
    /**
     * The enabled feature registers the front-end hook at priority 100.
     *
     * @return void
     */
    public function testEnabledFeatureRegistersHook(): void
    {
        $this->expectNotToPerformAssertions();

        expect('add_action')
            ->once()
            ->with('wp_enqueue_scripts', 'Cleanup\\dequeue_frontend_jquery_scripts', 100);

        \Cleanup\dequeue_frontend_jquery(true);
    }

    /**
     * The worker deregisters both jQuery handles.
     *
     * @return void
     */
    public function testWorkerDeregistersJquery(): void
    {
        $this->expectNotToPerformAssertions();

        expect('wp_deregister_script')->once()->with('jquery');
        expect('wp_deregister_script')->once()->with('jquery-migrate');

        \Cleanup\dequeue_frontend_jquery_scripts();
    }
}
