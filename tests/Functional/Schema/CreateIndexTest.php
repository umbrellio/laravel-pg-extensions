<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Schema;

use Closure;
use Generator;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Helpers\IndexAssertions;
use Umbrellio\Postgres\Helpers\TableAssertions;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class CreateIndexTest extends FunctionalTestCase
{
    use DatabaseTransactions, IndexAssertions, TableAssertions, InteractsWithDatabase;

    /**
     * @test
     */
    public function createIndexIfNotExists(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            if (!$table->hasIndex(['name'], true)) {
                $table->unique(['name']);
            }
        });

        $this->seeTable('test_table');

        Schema::table('test_table', function (Blueprint $table) {
            if (!$table->hasIndex(['name'], true)) {
                $table->unique(['name']);
            }
        });

        $this->seeIndex('test_table_name_unique');
    }

    /**
     * @test
     * @group WithSchema
     */
    public function createIndexWithSchema(): void
    {
        $this->createIndexDefinition();
        $this->assertSameIndex(
            'test_table_name_unique',
            'CREATE UNIQUE INDEX test_table_name_unique ON public.test_table USING btree (name)'
        );
    }

    /**
     * @test
     * @group WithoutSchema
     */
    public function createIndexWithoutSchema(): void
    {
        $this->createIndexDefinition();
        $this->assertSameIndex(
            'test_table_name_unique',
            'CREATE UNIQUE INDEX test_table_name_unique ON test_table USING btree (name)'
        );
    }

    /**
     * @test
     * @dataProvider provideIndexes
     */
    public function createPartialUnique(string $expected, Closure $callback): void
    {
        Schema::create('test_table', function (Blueprint $table) use ($callback) {
            $table->increments('id');
            $table->string('name');
            $table->string('code');
            $table->integer('phone');
            $table->boolean('enabled');
            $table->integer('icq');
            $table->softDeletes();

            $callback($table);
        });

        $this->seeTable('test_table');
        $this->assertRegExpIndex('test_table_name_unique', '/' . $this->getDummyIndex() . $expected . '/');

        Schema::table('test_table', function (Blueprint $table) {
            if (!$this->existConstraintOnTable($table->getTable(), 'test_table_name_unique')) {
                $table->dropUniquePartial(['name']);
            } else {
                $table->dropUnique(['name']);
            }
        });

        $this->notSeeIndex('test_table_name_unique');
    }

    /**
     * @test
     */
    public function createSpecifyIndex(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->string('name')->index('specify_index_name');
        });

        $this->seeTable('test_table');

        $this->assertRegExpIndex(
            'specify_index_name',
            '/CREATE INDEX specify_index_name ON (public.)?test_table USING btree \(name\)/'
        );
    }

    public function provideIndexes(): Generator
    {
        yield ['', function (Blueprint $table) {
            $table->uniquePartial('name');
        }];
        yield [
            ' WHERE \(deleted_at IS NULL\)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereNull('deleted_at');
            },
        ];
        yield [
            ' WHERE \(deleted_at IS NOT NULL\)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereNotNull('deleted_at');
            },
        ];
        yield [
            ' WHERE \(phone = 1234\)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->where('phone', '=', 1234);
            },
        ];
        yield [
            " WHERE \(\(code\)::text = 'test'::text\)",
            function (Blueprint $table) {
                $table->uniquePartial('name')->where('code', '=', 'test');
            },
        ];
        yield [
            ' WHERE \(\(phone >= 1\) AND \(phone <= 2\)\)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereBetween('phone', [1, 2]);
            },
        ];
        yield [
            ' WHERE \(\(phone < 1\) OR \(phone > 2\)\)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereNotBetween('phone', [1, 2]);
            },
        ];
        yield [
            ' WHERE \(phone <> icq\)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereColumn('phone', '<>', 'icq');
            },
        ];
        yield [
            ' WHERE \(\(phone = 1\) AND \(icq < 2\)\)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereRaw('phone = ? and icq < ?', [1, 2]);
            },
        ];
        yield [
            ' WHERE \(phone = ANY \(ARRAY\[1, 2, 4\]\)\)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereIn('phone', [1, 2, 4]);
            },
        ];
        yield [
            ' WHERE \(0 = 1\)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereIn('phone', []);
            },
        ];
        yield [
            ' WHERE \(phone <> ALL \(ARRAY\[1, 2, 4\]\)\)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereNotIn('phone', [1, 2, 4]);
            },
        ];
        yield [
            ' WHERE \(1 = 1\)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereNotIn('phone', []);
            },
        ];
    }

    /**
     * @test
     */
    public function addExcludeConstraints(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist');

        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->integer('period_type_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->softDeletes();

            $table
                ->exclude(['period_start', 'period_end'])
                ->using('period_type_id', '=')
                ->using('daterange(period_start, period_end)', '&&')
                ->method('gist')
                ->whereNull('deleted_at');
        });

        $this->seeConstraint('test_table', 'test_table_period_start_period_end_excl');

        Schema::table('test_table', function (Blueprint $table) {
            $table->dropExclude(['period_start', 'period_end']);
        });

        $this->dontSeeConstraint('test_table', 'test_table_period_start_period_end_excl');
    }

    /**
     * @test
     */
    public function addCheckConstraints(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('period_type_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->softDeletes();

            $table
                ->check(['period_start', 'period_end'])
                ->whereColumn('period_end', '>', 'period_start')
                ->whereIn('period_type_id', [1, 2, 3]);
        });

        foreach ($this->provideSuccessData() as [$period_type_id, $period_start, $period_end]) {
            $data = compact('period_type_id', 'period_start', 'period_end');
            DB::table('test_table')->insert($data);
            $this->assertDatabaseHas('test_table', $data);
        }

        foreach ($this->provideWrongData() as [$period_type_id, $period_start, $period_end]) {
            $data = compact('period_type_id', 'period_start', 'period_end');
            $this->expectException(QueryException::class);
            DB::table('test_table')->insert($data);
        }
    }

    /**
     * @test
     */
    public function dropCheckConstraints(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('period_type_id');
            $table
                ->check(['period_type_id'])
                ->whereNotNull('period_type_id');
        });

        $this->seeConstraint('test_table', 'test_table_period_type_id_chk');

        Schema::table('test_table', function (Blueprint $table) {
            $table->dropCheck(['period_type_id']);
        });

        $this->dontSeeConstraint('test_table', 'test_table_period_type_id_chk');
    }

    protected function getDummyIndex(): string
    {
        return 'CREATE UNIQUE INDEX test_table_name_unique ON (public.)?test_table USING btree \(name\)';
    }

    private function createIndexDefinition(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            if (!$table->hasIndex(['name'], true)) {
                $table->unique(['name']);
            }
        });

        $this->seeTable('test_table');

        Schema::table('test_table', function (Blueprint $table) {
            if (!$table->hasIndex(['name'], true)) {
                $table->unique(['name']);
            }
        });

        $this->seeIndex('test_table_name_unique');
    }

    private function provideSuccessData(): Generator
    {
        yield [1, '2019-01-01', '2019-01-31'];
        yield [2, '2019-02-15', '2019-04-20'];
        yield [3, '2019-03-07', '2019-06-24'];
    }

    private function provideWrongData(): Generator
    {
        yield [4, '2019-01-01', '2019-01-31'];
        yield [1, '2019-07-15', '2019-04-20'];
        yield [2, '2019-12-07', '2019-06-24'];
    }
}
