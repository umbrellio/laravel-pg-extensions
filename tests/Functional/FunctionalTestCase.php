<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Umbrellio\Postgres\Tests\TestCase;

abstract class FunctionalTestCase extends TestCase
{
    use RefreshDatabase;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'pgsql',
            'host' => env('TEST_DB_HOST', 'localhost'),
            'port' => env('TEST_DB_PORT', 5432),
            'database' => env('TEST_DB', 'testing'),
            'username' => env('TEST_DB_USER', 'user'),
            'password' => env('TEST_DB_PASSWORD', 'pass'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ]);
    }
}
