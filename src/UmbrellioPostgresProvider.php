<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Support\Facades\DB;
use Umbrellio\Postgres\Connectors\ConnectionFactory;
use Umbrellio\Postgres\Schema\Extensions\AbstractExtension;

class UmbrellioPostgresProvider extends DatabaseServiceProvider
{
    private static $extensions = [];

    /**
     * @param AbstractExtension|string $extension
     */
    public static function registerExtension(string $extension): void
    {
        static::$extensions[$extension::getName()] = $extension;
    }

    public function boot()
    {
        parent::boot();
        $this->registerExtensions();
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

    /**
     * @codeCoverageIgnore
     */
    protected function registerExtensions(): void
    {
        /** @var PostgresConnection $connection */
        $connection = DB::connection();
        collect(static::$extensions)->each(function ($extension, $key) use ($connection) {
            /** @var AbstractExtension $extension */
            $extension::register();
            foreach ($extension::getTypes() as $type => $typeClass) {
                $connection->getSchemaBuilder()->registerCustomDoctrineType($typeClass, $type, $type);
            }
        });
    }
}
