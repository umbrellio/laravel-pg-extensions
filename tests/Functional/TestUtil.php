<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use function explode;
use const PHP_EOL;
use Illuminate\Support\Facades\DB;

class TestUtil
{
    private static $initialized = false;

    public static function createDatabase()
    {
        if (self::hasRequiredConnectionParams() && !self::$initialized) {
            self::initializeDatabase();
            self::$initialized = true;
        }
    }

    public static function getConnection(): Connection
    {
        static::createDatabase();

        $conn = static::getDoctrineConnection();

        self::addDbEventSubscribers($conn);

        return $conn;
    }

    public static function getTempConnection(): Connection
    {
        return static::getDoctrineConnection('temporary');
    }

    public static function getParamsForMainConnection(): array
    {
        $connectionParams = [
            'driver' => $GLOBALS['db_type'] ?? 'pdo_pgsql',
            'user' => $GLOBALS['db_username'],
            'password' => $GLOBALS['db_password'],
            'host' => $GLOBALS['db_host'],
            'dbname' => $GLOBALS['db_name'],
            'port' => $GLOBALS['db_port'],
        ];

        if (isset($GLOBALS['db_server'])) {
            $connectionParams['server'] = $GLOBALS['db_server'];
        }

        if (isset($GLOBALS['db_unix_socket'])) {
            $connectionParams['unix_socket'] = $GLOBALS['db_unix_socket'];
        }

        return $connectionParams;
    }

    public static function getParamsForTemporaryConnection(): array
    {
        $connectionParams = [
            'driver' => $GLOBALS['tmpdb_type'],
            'user' => $GLOBALS['tmpdb_username'],
            'password' => $GLOBALS['tmpdb_password'],
            'host' => $GLOBALS['tmpdb_host'],
            'dbname' => null,
            'port' => $GLOBALS['tmpdb_port'],
        ];

        if (isset($GLOBALS['tmpdb_name'])) {
            $connectionParams['dbname'] = $GLOBALS['tmpdb_name'];
        }

        if (isset($GLOBALS['tmpdb_server'])) {
            $connectionParams['server'] = $GLOBALS['tmpdb_server'];
        }

        if (isset($GLOBALS['tmpdb_unix_socket'])) {
            $connectionParams['unix_socket'] = $GLOBALS['tmpdb_unix_socket'];
        }

        return $connectionParams;
    }

    private static function hasRequiredConnectionParams(): bool
    {
        return isset(
            $GLOBALS['db_type'],
            $GLOBALS['db_username'],
            $GLOBALS['db_password'],
            $GLOBALS['db_host'],
            $GLOBALS['db_name'],
            $GLOBALS['db_port']
        )
        && isset(
            $GLOBALS['tmpdb_type'],
            $GLOBALS['tmpdb_username'],
            $GLOBALS['tmpdb_password'],
            $GLOBALS['tmpdb_host'],
            $GLOBALS['tmpdb_port']
        );
    }

    private static function initializeDatabase(): void
    {
        $realConn = static::createDoctrineConnection(self::getParamsForMainConnection());
        $tmpConn = static::createDoctrineConnection(self::getParamsForTemporaryConnection());

        $platform = $tmpConn->getDatabasePlatform();

        if ($platform->supportsCreateDropDatabase()) {
            $dbname = $realConn->getDatabase();
            $realConn->close();

            $tmpConn->getSchemaManager()->dropAndCreateDatabase($dbname);

            $tmpConn->close();
        } else {
            $sm = $realConn->getSchemaManager();

            $schema = $sm->createSchema();
            $stmts = $schema->toDropSql($realConn->getDatabasePlatform());

            foreach ($stmts as $stmt) {
                $realConn->exec($stmt);
            }
        }
    }

    private static function addDbEventSubscribers(Connection $conn): void
    {
        if (!isset($GLOBALS['db_event_subscribers'])) {
            return;
        }

        $evm = $conn->getEventManager();
        foreach (explode(',', $GLOBALS['db_event_subscribers']) as $subscriberClass) {
            $subscriberInstance = new $subscriberClass();
            $evm->addEventSubscriber($subscriberInstance);
        }
    }

    private static function getDoctrineConnection(string $name = null): Connection
    {
        $connection = DB::connection($name);
        return $connection->getDoctrineConnection();
    }

    private static function createDoctrineConnection($params): Connection
    {
        return DriverManager::getConnection($params);
    }
}
