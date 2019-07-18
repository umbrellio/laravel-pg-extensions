<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\DBAL\Platform;

use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Umbrellio\Postgres\Tests\Functional\DbalFunctionalTestCase;
use function sprintf;

class DefaultExpressionTest extends DbalFunctionalTestCase
{
    public function testCurrentDate() : void
    {
        $this->assertDefaultExpression(Types::DATE_MUTABLE, static function (AbstractPlatform $platform) : string {
            return $platform->getCurrentDateSQL();
        });
    }

    public function testCurrentTime() : void
    {
        $this->assertDefaultExpression(Types::TIME_MUTABLE, static function (AbstractPlatform $platform) : string {
            return $platform->getCurrentTimeSQL();
        });
    }

    public function testCurrentTimestamp() : void
    {
        $this->assertDefaultExpression(Types::DATETIME_MUTABLE, static function (AbstractPlatform $platform) : string {
            return $platform->getCurrentTimestampSQL();
        });
    }

    private function assertDefaultExpression(string $type, callable $expression) : void
    {
        $platform   = $this->connection->getDatabasePlatform();
        $defaultSql = $expression($platform, $this);

        $table = new Table('default_expr_test');
        $table->addColumn('actual_value', $type);
        $table->addColumn('default_value', $type, ['default' => $defaultSql]);
        $this->connection->getSchemaManager()->dropAndCreateTable($table);

        $this->connection->exec(
            sprintf(
                'INSERT INTO default_expr_test (actual_value) VALUES (%s)',
                $defaultSql
            )
        );

        [$actualValue, $defaultValue] = $this->connection->query(
            'SELECT default_value, actual_value FROM default_expr_test'
        )->fetch(FetchMode::NUMERIC);

        self::assertEquals($actualValue, $defaultValue);
    }
}
