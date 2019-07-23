<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Schema\Blueprint;

use Closure;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Doctrine\Types\TsRangeType;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class AddColumnsTest extends FunctionalTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * @dataProvider provideRangeTypes
     */
    public function addColumnRangeFormat(string $expectedFormat, Closure $callback): void
    {
        Schema::create('test_table', function (Blueprint $table) use ($callback) {
            $callback($table, 'field_range');
        });
        $this->assertSame($expectedFormat, Schema::getColumnType('test_table', 'field_range'));
    }

    public function provideRangeTypes(): Generator
    {
        yield [TsRangeType::TYPE_NAME, function (Blueprint $table, string $column) {
            $table->tsRange($column);
        }];
        yield ['text', function (Blueprint $table, string $column) {
            $table->tsVector($column);
        }];
    }
}
