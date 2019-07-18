<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOPgSql\Driver;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\PostgreSQL91Platform;
use Doctrine\DBAL\Platforms\PostgreSQL92Platform;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Illuminate\Support\Traits\Macroable;
use Umbrellio\Postgres\Schema\Drivers\Platforms\SQL100Platform;
use Umbrellio\Postgres\Schema\Drivers\Platforms\SQL91Platform;
use Umbrellio\Postgres\Schema\Drivers\Platforms\SQL92Platform;
use Umbrellio\Postgres\Schema\Drivers\Platforms\SQL94Platform;
use Umbrellio\Postgres\Schema\Drivers\Platforms\SQLPlatform;
use Umbrellio\Postgres\Schema\SQLSchemaManager;

class DoctrineDriver extends Driver
{
    use Macroable;

    public function getDatabasePlatform()
    {
        return new SQLPlatform();
    }

    public function getSchemaManager(Connection $conn)
    {
        return new SQLSchemaManager($conn);
    }

    public function createDatabasePlatformForVersion($version)
    {
        $platform = parent::createDatabasePlatformForVersion($version);
        switch (true) {
            case $platform instanceof PostgreSQL100Platform:
                return new SQL100Platform();
            case $platform instanceof PostgreSQL94Platform:
                return new SQL94Platform();
            case $platform instanceof PostgreSQL92Platform:
                return new SQL92Platform();
            case $platform instanceof PostgreSQL91Platform:
                return new SQL91Platform();
            default:
                return $this->getDatabasePlatform();
        }
    }
}
