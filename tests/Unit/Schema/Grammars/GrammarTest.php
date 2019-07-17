<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Schema\Grammars;

use Mockery;
use Umbrellio\Postgres\PostgresConnection;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;
use Umbrellio\Postgres\Tests\TestCase;

class GrammarTest extends TestCase
{
    /** @test */
    public function addingGinIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->gin('foo');
        $statements = $blueprint->toSql($this->getConnectionMock(), $this->getGrammar());
        $this->assertCount(1, $statements);
        $this->assertStringContainsString('CREATE INDEX', $statements[0]);
        $this->assertStringContainsString('GIN("foo")', $statements[0]);
    }

    /** @test */
    public function addingGistIndex()
    {
        $blueprint = new Blueprint('test');
        $blueprint->gist('foo');
        $statements = $blueprint->toSql($this->getConnectionMock(), $this->getGrammar());
        $this->assertCount(1, $statements);
        $this->assertStringContainsString('CREATE INDEX', $statements[0]);
        $this->assertStringContainsString('GIST("foo")', $statements[0]);
    }

    protected function getConnectionMock()
    {
        return Mockery::mock(PostgresConnection::class);
    }

    protected function getGrammar()
    {
        return new PostgresGrammar();
    }
}
