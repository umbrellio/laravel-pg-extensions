<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Support\Facades\DB;
use Umbrellio\Postgres\Connectors\ConnectionFactory;

class UmbrellioPostgresProvider extends DatabaseServiceProvider
{
    public function boot()
    {
        parent::boot();
        $this->registerDoctrineTypes();
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

    private function registerDoctrineTypes(): void
    {
        $connection = DB::connection('pgsql');
        foreach (config('database.doctrine') as $config) {
            foreach ($config['mapping_types'] as $type => $className) {
                $connection->getSchemaBuilder()->registerCustomDoctrineType($className, $type, $type);
            }
        }
    }
}
