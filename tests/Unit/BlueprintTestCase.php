<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit;

use Umbrellio\Postgres\PostgresConnection;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;
use Umbrellio\Postgres\Tests\TestCase;

class BlueprintTestCase extends TestCase
{
    /** @var Blueprint */
    protected $blueprint;
    /** @var PostgresConnection */
    protected $postgresConnection;
    /** @var PostgresGrammar */
    protected $postgresGrammar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->blueprint = new Blueprint('test_table');
        $this->postgresConnection = $this->createMock(PostgresConnection::class);
        $this->postgresGrammar = new PostgresGrammar();
    }

    /**
     * @param string|array $sql
     */
    protected function assertSameSql($sql): void
    {
        $this->assertSame((array) $sql, $this->runToSql());
    }

    /**
     * @param string|array $regexpExpected
     */
    protected function assertRegExpSql($regexpExpected): void
    {
        foreach ($this->runToSql() as $sql) {
            $this->assertRegExp($regexpExpected, $sql);
        }
    }

    protected function runToSql(): array
    {
        return $this->blueprint->toSql($this->postgresConnection, $this->postgresGrammar);
    }
}
