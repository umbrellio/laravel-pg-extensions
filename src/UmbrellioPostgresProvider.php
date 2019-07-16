<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\DatabaseServiceProvider;
use Umbrellio\Postgres\Connectors\ConnectionFactory;
use Umbrellio\Postgres\Extensions\AbstractExtension;
use Umbrellio\Postgres\Extensions\Exceptions\ExtensionInvalidException;

class UmbrellioPostgresProvider extends DatabaseServiceProvider
{
    private static $extensions = [];

    /**
     * @param AbstractExtension|string $extension
     * @codeCoverageIgnore
     */
    final public static function registerExtension(string $extension): void
    {
        if (!is_subclass_of($extension, AbstractExtension::class)) {
            throw new ExtensionInvalidException(sprintf(
                'Class %s must be implemented from %s',
                $extension,
                AbstractExtension::class
            ));
        }
        self::$extensions[$extension::getName()] = $extension;
    }

    public function boot()
    {
        parent::boot();
        $this->registerExtensions();
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

    /**
     * @codeCoverageIgnore
     */
    final private function registerExtensions(): void
    {
        /** @var PostgresConnection $connection */
        $connection = $this->app['db'];
        collect(self::$extensions)->each(function ($extension, $key) use ($connection) {
            /** @var AbstractExtension $extension */
            $extension::register();
            foreach ($extension::getTypes() as $type => $typeClass) {
                $connection->getSchemaBuilder()->registerCustomDoctrineType($typeClass, $type, $type);
            }
        });
    }
}
