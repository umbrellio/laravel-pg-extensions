<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Unit\Schema\Blueprint;

use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Umbrellio\Postgres\Tests\Unit\BlueprintTestCase;

class PartitionTest extends BlueprintTestCase
{
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

        $this->assertSameSql(sprintf(
            'alter table "test_table" attach partition some_partition for values from (\'%s\') to (\'%s\')',
            $today->toDateTimeString(),
            $tomorrow->toDateTimeString()
        ));
    }
}
