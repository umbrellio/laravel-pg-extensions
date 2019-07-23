<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;

abstract class FunctionalTestCase extends TestCase
{
    protected function setUp(): void
    {
        if (!$this->app) {
            putenv('APP_ENV=testing');
            $this->app = $this->createApplication();
        }

        parent::setUp();

        Facade::clearResolvedInstances();
    }
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.default', 'main');
        $this->setConnectionConfig($app, 'main', $this->getParamsForConnection());
    }

    protected function assertCommentOnColumn(string $table, string $column, ?string $expected = null): void
    {
        $comment = $this->getCommentListing($table, $column);

        if ($expected === null) {
            $this->assertNull($comment);
        }
        $this->assertSame($expected, $comment);
    }

    protected function assertDefaultOnColumn(string $table, string $column, ?string $expected = null): void
    {
        $defaultValue = $this->getDefaultListing($table, $column);

        if ($expected === null) {
            $this->assertNull($defaultValue);
        }
        $this->assertSame($expected, $defaultValue);
    }

    private function setConnectionConfig($app, $name, $params): void
    {
        $app['config']->set('database.connections.' . $name, [
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
    }

    private function getParamsForConnection(): array
    {
        $connectionParams = [
            'driver' => $GLOBALS['db_type'] ?? 'pdo_pgsql',
            'user' => $GLOBALS['db_username'],
            'password' => $GLOBALS['db_password'],
            'host' => $GLOBALS['db_host'],
            'database' => $GLOBALS['db_database'],
            'port' => $GLOBALS['db_port'],
        ];

        if (isset($GLOBALS['db_server'])) {
            $connectionParams['server'] = $GLOBALS['db_server'];
        }

        if (isset($GLOBALS['db_unix_socket'])) {
            $connectionParams['unix_socket'] = $GLOBALS['db_unix_socket'];
        }

        return $connectionParams;
    }

    private function getCommentListing(string $table, string $column)
    {
        $definition = DB::selectOne(
            '
                SELECT pgd.description FROM pg_catalog.pg_statio_all_tables AS st
                INNER JOIN pg_catalog.pg_description pgd ON (pgd.objoid = st.relid)
                INNER JOIN information_schema.columns c ON pgd.objsubid = c.ordinal_position AND c.table_schema = st.schemaname AND c.table_name = st.relname
                WHERE c.table_name = ? AND c.column_name = ?
            ',
            [$table, $column]
        );

        return $definition ? $definition->description : null;
    }

    private function getDefaultListing(string $table, string $column)
    {
        $definition = DB::selectOne(
            'SELECT column_default FROM information_schema.columns WHERE table_name = ? and column_name = ?',
            [$table, $column]
        );

        return $definition ? $definition->column_default : null;
    }
}
