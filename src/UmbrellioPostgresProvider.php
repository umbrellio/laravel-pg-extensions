<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\DatabaseServiceProvider;
use Umbrellio\Postgres\Connectors\ConnectionFactory;

class UmbrellioPostgresProvider extends DatabaseServiceProvider
{
    public function register()
    {
        parent::register();

        if (config()->has('database.doctrine')) {
            // @codeCoverageIgnoreStart
            foreach (config('database.doctrine') as $config) {
                foreach ($config['mapping_types'] as $type => $className) {
                    $this->getSchemaBuilder()->registerCustomDoctrineType($className, $type, $type);
                }
            }
            // @codeCoverageIgnoreEnd
        }
    }

    protected function registerConnectionServices(): void
    {
        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });

        $this->app->singleton('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });
    }
}
