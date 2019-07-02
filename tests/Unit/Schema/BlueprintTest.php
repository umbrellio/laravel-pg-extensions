<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Unit\Schema;

use Umbrellio\Postgres\PostgresConnection;
use Umbrellio\Postgres\Schema\Blueprint;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;
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

    public function testDetachPartition(): void
    {
        $this->blueprint->detachPartition('some_partition');

        $this->assertSameSql('alter table "test_table" detach partition some_partition');
    }

    public function testAttachPartitionRangeInt(): void
    {
        $this->blueprint->attachPartition('some_partition')->range([
            'from' => 10,
            'to' => 100,
        ]);

        $this->assertSameSql('alter table "test_table" attach partition some_partition for values from (10) to (100)');
    }

    public function testAttachPartitionFailedWithoutForValuesPart(): void
    {
        $this->blueprint->attachPartition('some_partition');

        $this->expectException(InvalidArgumentException::class);
        $this->runToSql();
    }

    public function testAttachPartitionRangeDates(): void
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $this->blueprint->attachPartition('some_partition')->range([
            'from' => $today,
            'to' => $tomorrow,
        ]);

        $this->assertSameSql(
            'alter table "test_table" attach partition some_partition '
            . "for values from ('{$today->toDateTimeString()}') to ('{$tomorrow->toDateTimeString()}')");
    }

    private function assertSameSql(string $sql): void
    {
        $this->assertSame([$sql], $this->runToSql());
    }

    private function runToSql(): array
    {
        return $this->blueprint->toSql($this->createMock(PostgresConnection::class), new PostgresGrammar());
    }
}
