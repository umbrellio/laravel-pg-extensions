<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\DatabaseServiceProvider;
use Umbrellio\Postgres\Connectors\ConnectionFactory;
use Umbrellio\Postgres\Doctrine\RangeExtension;
use Umbrellio\Postgres\Doctrine\VectorExtension;

class UmbrellioPostgresProvider extends DatabaseServiceProvider
{
    public function register()
    {
        parent::register();
        PostgresConnection::registerExtension(RangeExtension::class);
        PostgresConnection::registerExtension(VectorExtension::class);
    }

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
    }
}
