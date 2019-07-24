<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Schema\Blueprint;

use Closure;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\Functional\Helpers\ColumnAssertions;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class AddColumnsTest extends FunctionalTestCase
{
    use DatabaseTransactions, ColumnAssertions;

    /**
     * @test
     * @dataProvider provideRangeTypes
     */
    public function addColumnRangeFormat(string $type, Closure $callback): void
    {
        Schema::create('test_table', function (Blueprint $table) use ($callback) {
            $callback($table, 'field_range');
        });
        $this->assertTypeColumn('test_table', 'field_range', $type);
    }

    public function provideRangeTypes(): Generator
    {
        yield ['tsrange', function (Blueprint $table, string $column) {
            $table->tsRange($column);
        }];
        yield ['text', function (Blueprint $table, string $column) {
            $table->tsVector($column);
        }];
    }
}
