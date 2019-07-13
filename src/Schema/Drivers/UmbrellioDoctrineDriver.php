<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOPgSql\Driver;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\PostgreSQL91Platform;
use Doctrine\DBAL\Platforms\PostgreSQL92Platform;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Umbrellio\Postgres\Schema\Drivers\Platforms\UmbrellioSQL100Platform;
use Umbrellio\Postgres\Schema\Drivers\Platforms\UmbrellioSQL91Platform;
use Umbrellio\Postgres\Schema\Drivers\Platforms\UmbrellioSQL92Platform;
use Umbrellio\Postgres\Schema\Drivers\Platforms\UmbrellioSQL94Platform;
use Umbrellio\Postgres\Schema\UmbrellioSqlSchemaManager;

/**
 * @codeCoverageIgnore
 */
class UmbrellioDoctrineDriver extends Driver
{
    public function getDatabasePlatform()
    {
        return new UmbrellioSqlPlatform();
    }

    public function getSchemaManager(Connection $conn)
    {
        return new UmbrellioSqlSchemaManager($conn);
    }

    public function createDatabasePlatformForVersion($version)
    {
        $platform = parent::createDatabasePlatformForVersion($version);
        switch (true) {
            case $platform instanceof PostgreSQL100Platform:
                return new UmbrellioSQL100Platform();
            case $platform instanceof PostgreSQL94Platform:
                return new UmbrellioSQL94Platform();
            case $platform instanceof PostgreSQL92Platform:
                return new UmbrellioSQL92Platform();
            case $platform instanceof PostgreSQL91Platform:
                return new UmbrellioSQL91Platform();
            default:
                return $this->getDatabasePlatform();
        }
    }
}
