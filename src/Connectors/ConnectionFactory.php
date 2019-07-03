<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Connectors;

use Illuminate\Database\Connectors\ConnectionFactory as ConnectionFactoryBase;
use Umbrellio\Postgres\PostgresConnection;

class ConnectionFactory extends ConnectionFactoryBase
{
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        return new PostgresConnection($connection, $database, $prefix, $config);
    }
}
