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
            'user' => $GLOBALS['db_username'],
            'password' => $GLOBALS['db_password'],
            'host' => $GLOBALS['db_host'],
            'database' => $GLOBALS['db_database'],
            'port' => $GLOBALS['db_port'],
        ];
    }
}
