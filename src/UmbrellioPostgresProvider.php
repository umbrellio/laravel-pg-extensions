<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Database\DatabaseTransactionsManager;
use Umbrellio\Postgres\Connectors\ConnectionFactory;

class UmbrellioPostgresProvider extends DatabaseServiceProvider
{
    /**
     * @codeCoverageIgnore
     */
    protected function registerConnectionServices(): void
    {
        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });

        $this->app->singleton('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });

        $this->app->bind('db.connection', function ($app) {
            return $app['db']->connection();
        });

        $this->app->bind('db.schema', function ($app) {
            return $app['db']->connection()->getSchemaBuilder();
        });

        $this->app->singleton('db.transactions', function ($app) {
            return new DatabaseTransactionsManager();
        });
    }
}
