<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;

class UniqueIndexTest extends FunctionalTestCase
{
    /**
     * @test
     * @dataProvider provideIndexes
     */
    public function createPartialUniqueWithNull($expected, $callback): void
    {
        Schema::create('test_table', function (Blueprint $table) use ($callback) {
            $table->increments('id');
            $table->string('name');
            $table->string('code');
            $table->integer('phone');
            $table->softDeletes();
            $callback($table);
        });

        $this->assertTrue(Schema::hasTable('test_table'));

        $indexes = $this->getIndexByName('test_table_name_unique');

        $this->assertTrue(isset($indexes->indexdef));
        $this->assertSame($this->getDummyIndex() . $expected, $indexes->indexdef);
    }

    public function provideIndexes(): Generator
    {
        yield ['', function (Blueprint $table) {
            $table->unique('name');
        }];
        yield [
            ' WHERE (deleted_at IS NULL)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereNull('deleted_at');
            },
        ];
        yield [
            ' WHERE (deleted_at IS NOT NULL)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereNotNull('deleted_at');
            },
        ];
        yield [
            ' WHERE (phone = 1234)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->where('phone', '=', 1234);
            },
        ];
        yield [
            ' WHERE (phone = 1234)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->where('phone', '=', 1234);
            },
        ];
        yield [
            ' WHERE ((phone >= 1) AND (phone <= 2))',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereBetween('phone', [1, 2]);
            },
        ];
        yield [
            ' WHERE ((phone < 1) OR (phone > 2))',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereNotBetween('phone', [1, 2]);
            },
        ];
    }

    protected function getDummyIndex()
    {
        return 'CREATE UNIQUE INDEX test_table_name_unique ON public.test_table USING btree (name)';
    }

    protected function getIndexByName($name)
    {
        return collect(DB::select("SELECT indexdef FROM pg_indexes WHERE  indexname = '{$name}'"))->first();
    }
}
