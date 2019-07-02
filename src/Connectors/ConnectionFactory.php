<?php

namespace Umbrellio\Postgres\Connectors;

use Illuminate\Database\Connectors\ConnectionFactory as ConnectionFactoryBase;
use Umbrellio\Postgres\PostgresConnection;

class ConnectionFactory extends ConnectionFactoryBase
{
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = array())
    {
        if ($this->container->bound($key = "db.connection.{$driver}")) {
            return $this->container->make($key, array($connection, $database, $prefix, $config));
        }
        if ($driver === 'pgsql') {
            return new PostgresConnection($connection, $database, $prefix, $config);
        }
        return parent::createConnection($driver, $connection, $database, $prefix, $config);
    }
}
