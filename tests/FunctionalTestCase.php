<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests;

use Illuminate\Support\Facades\Facade;
use PDO;

abstract class FunctionalTestCase extends TestCase
{
    protected $emulatePrepares = false;

    protected function setUp(): void
    {
        parent::setUp();

        Facade::clearResolvedInstances();
    }

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $params = $this->getConnectionParams();

        $app['config']->set('database.default', 'main');
        $app['config']->set('database.connections.main', [
            'driver' => 'pgsql',
            'host' => $params['host'],
            'port' => (int) $params['port'],
            'database' => $params['database'],
            'username' => $params['user'],
            'password' => $params['password'],
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ]);

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => __DIR__ . '/_data/database.sqlite',
        ]);

        if ($this->emulatePrepares) {
            $app['config']->set('database.connections.main.options', [
                PDO::ATTR_EMULATE_PREPARES => true,
            ]);
        }
    }

    private function getConnectionParams(): array
    {
        return [
            'driver' => $GLOBALS['db_type'] ?? 'pdo_pgsql',
            'user' => env('POSTGRES_USER', $GLOBALS['db_username']),
            'password' => env('POSTGRES_PASSWORD', $GLOBALS['db_password']),
            'host' => env('POSTGRES_HOST', $GLOBALS['db_host']),
            'database' => env('POSTGRES_DB', $GLOBALS['db_database']),
            'port' => env('POSTGRES_PORT', $GLOBALS['db_port']),
        ];
    }
}
