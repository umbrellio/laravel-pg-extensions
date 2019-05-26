<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Unit\Schema;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Umbrellio\Postgres\PostgresSchemaGrammar;
use Umbrellio\Postgres\Tests\TestCase;

class BlueprintTest extends TestCase
{
    /** @var Blueprint */
    private $blueprint;

    protected function setUp(): void
    {
        parent::setUp();

        $this->blueprint = new Blueprint('test_table');
    }

    /** @test */
    public function detachPartition(): void
    {
        $this->blueprint->detachPartition('some_partition');

        $this->assertSameSql('alter table "test_table" detach partition some_partition');
    }

    private function assertSameSql(string $sql): void
    {
        $this->assertSame(
            [$sql],
            $this->blueprint->toSql($this->createMock(Connection::class), new PostgresSchemaGrammar())
        );
    }
}
