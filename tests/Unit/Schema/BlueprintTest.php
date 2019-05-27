<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Unit\Schema;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Umbrellio\Postgres\Commands\CreateCommand;
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

    /** @test */
    public function attachPartitionRangeInt(): void
    {
        $this->blueprint->attachPartition('some_partition')->range([
            'from' => 10,
            'to' => 100,
        ]);

        $this->assertSameSql('alter table "test_table" attach partition some_partition for values from (10) to (100)');
    }

    /** @test */
    public function attachPartitionFailedWithoutForValuesPart(): void
    {
        $this->blueprint->attachPartition('some_partition');

        $this->expectException(InvalidArgumentException::class);
        $this->runToSql();
    }

    /** @test */
    public function attachPartitionRangeDates(): void
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

    /** @test */
    public function createIfNotExists(): void
    {
        /** @var CreateCommand $create */
        $create = $this->blueprint->create();
        $create->ifNotExists();

        $this->blueprint->increments('id');

        $this->assertSameSql('create table if not exists "test_table" ("id" serial primary key not null)');
    }

    /** @test */
    public function createWitLikeIncludingAll(): void
    {
        /** @var CreateCommand $create */
        $create = $this->blueprint->create();
        $create->like('other_table')->includingAll();

        $this->assertSameSql('create table "test_table" (like "other_table" including all)');
    }

    private function assertSameSql(string $sql): void
    {
        $this->assertSame([$sql], $this->runToSql());
    }

    private function runToSql(): array
    {
        return $this->blueprint->toSql($this->createMock(Connection::class), new PostgresSchemaGrammar());
    }
}
