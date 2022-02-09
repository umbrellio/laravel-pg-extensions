<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;
use Umbrellio\Postgres\PostgresConnection;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;

/**
 * @mixin TestCase
 *
 * @property Blueprint $blueprint
 * @property PostgresConnection $postgresConnection
 * @property PostgresGrammar $postgresGrammar
 */
trait BlueprintAssertions
{
    protected $blueprint;

    protected $postgresConnection;

    protected $postgresGrammar;

    public function initializeMock(string $table)
    {
        $this->blueprint = new Blueprint($table);
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

    protected function assertRegExpSql(string $regexpExpected): void
    {
        foreach ($this->runToSql() as $sql) {
            $this->assertMatchesRegularExpression($regexpExpected, $sql);
        }
    }

    private function runToSql(): array
    {
        return $this->blueprint->toSql($this->postgresConnection, $this->postgresGrammar);
    }
}
