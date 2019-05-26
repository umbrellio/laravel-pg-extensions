<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class UmbrellioPostgresProvider extends ServiceProvider
{
    public function register(): void
    {
        Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
            return new PostgresConnection($connection, $database, $prefix, $config);
        });
    }

    public function boot(): void
    {
        $this->loadBlueprint();
    }

    private function loadBlueprint(): void
    {
        require __DIR__ . '/Macros/blueprint.php';
    }
}
