<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Umbrellio\Postgres\Connectors\ConnectionFactory;

class UmbrellioPostgresProvider extends DatabaseServiceProvider
{
    public function register()
    {
        Model::clearBootedModels();

        $this->app->singleton('db.factory', static function ($app) {
            return new ConnectionFactory($app);
        });

        $this->app->singleton('db', static function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });

        $this->app->bind('db.connection', static function ($app) {
            return $app['db']->connection();
        });

        $this->registerEloquentFactory();

        $this->registerQueueableEntityResolver();
    }
}
