<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Unit\Schema\Blueprint;

use Closure;
use Generator;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\TestCase;
use Umbrellio\Postgres\Tests\Unit\Helpers\BlueprintAssertions;

class IndexTest extends TestCase
{
    use BlueprintAssertions;

    private const TABLE = 'test_table';

    protected function setUp(): void
    {
        parent::setUp();
        $this->initializeMock(static::TABLE);
    }

    /**
     * @test
     * @dataProvider provideExcludeConstraints
     */
    public function addExcludeConstraint(Closure $callback, string $expectedSQL): void
    {
        $callback($this->blueprint);
        $this->assertSameSql($expectedSQL);
    }

    public function provideExcludeConstraints(): Generator
    {
        yield [
            static function (Blueprint $table) {
                $table
                    ->exclude(['period_start', 'period_end'])
                    ->using('period_type_id', '=')
                    ->using('daterange(period_start, period_end)', '&&')
                    ->method('gist')
                    ->whereNull('deleted_at');
            },
            implode(' ', [
                'ALTER TABLE test_table ADD CONSTRAINT test_table_period_start_period_end_excl',
                'EXCLUDE USING gist (period_type_id WITH =, daterange(period_start, period_end) WITH &&)',
                'WHERE ("deleted_at" is null)',
            ]),
        ];
        yield [
            static function (Blueprint $table) {
                $table
                    ->exclude(['period_start', 'period_end'])
                    ->using('period_type_id', '=')
                    ->using('daterange(period_start, period_end)', '&&')
                    ->whereNull('deleted_at');
            },
            implode(' ', [
                'ALTER TABLE test_table ADD CONSTRAINT test_table_period_start_period_end_excl',
                'EXCLUDE (period_type_id WITH =, daterange(period_start, period_end) WITH &&)',
                'WHERE ("deleted_at" is null)',
            ]),
        ];
        yield [
            static function (Blueprint $table) {
                $table
                    ->exclude(['period_start', 'period_end'])
                    ->using('period_type_id', '=')
                    ->using('daterange(period_start, period_end)', '&&');
            },
            implode(' ', [
                'ALTER TABLE test_table ADD CONSTRAINT test_table_period_start_period_end_excl',
                'EXCLUDE (period_type_id WITH =, daterange(period_start, period_end) WITH &&)',
            ]),
        ];
        yield [
            static function (Blueprint $table) {
                $table
                    ->exclude(['period_start', 'period_end'])
                    ->using('period_type_id', '=')
                    ->using('daterange(period_start, period_end)', '&&')
                    ->tableSpace('excludeSpace');
            },
            implode(' ', [
                'ALTER TABLE test_table ADD CONSTRAINT test_table_period_start_period_end_excl',
                'EXCLUDE (period_type_id WITH =, daterange(period_start, period_end) WITH &&)',
                'USING INDEX TABLESPACE excludeSpace',
            ]),
        ];
        yield [
            static function (Blueprint $table) {
                $table
                    ->exclude(['period_start', 'period_end'])
                    ->using('period_type_id', '=')
                    ->using('daterange(period_start, period_end)', '&&')
                    ->with('some_arg', 1)
                    ->with('any_arg', 'some_value');
            },
            implode(' ', [
                'ALTER TABLE test_table ADD CONSTRAINT test_table_period_start_period_end_excl',
                'EXCLUDE (period_type_id WITH =, daterange(period_start, period_end) WITH &&)',
                "WITH (some_arg = 1, any_arg = 'some_value')",
            ]),
        ];
    }
}
