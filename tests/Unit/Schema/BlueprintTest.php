<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Unit\Schema;

use Generator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Umbrellio\Postgres\PostgresConnection;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;
use Umbrellio\Postgres\Tests\TestCase;
use Closure;

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

    /**
     * @test
     * @dataProvider provideDefaultValues
     */
    public function changeDefaultValue(string $expectedSql, Closure $callback): void
    {
        $callback($this->blueprint);

        $this->assertSameSql($expectedSql);
    }

    public function provideDefaultValues(): Generator
    {
        yield [
            "alter table \"test_table\" add column \"id\" bigint not null default nextval('test_table_id_seq'::regclass)",
            function (Blueprint $table) {
                $table->bigInteger('id')->default(DB::raw("nextval('test_table_id_seq'::regclass)"));
            },
        ];
        yield [
            "alter table \"test_table\" add column \"code\" varchar(255) not null default ''::character varying",
            function (Blueprint $table) {
                $table->string('code')->default(DB::raw("''::character varying"));
            },
        ];
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
