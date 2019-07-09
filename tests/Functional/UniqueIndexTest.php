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
            $table->boolean('enabled');
            $table->integer('icq');
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
            $table->uniquePartial('name');
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
            " WHERE ((code)::text = 'test'::text)",
            function (Blueprint $table) {
                $table->uniquePartial('name')->where('code', '=', 'test');
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
        yield [
            ' WHERE (phone <> icq)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereColumn('phone', '<>', 'icq');
            },
        ];
        yield [
            ' WHERE ((phone = 1) AND (icq < 2))',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereRaw('phone = ? and icq < ?', [1, 2]);
            },
        ];
        yield [
            ' WHERE (phone = ANY (ARRAY[1, 2, 4]))',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereIn('phone', [1, 2, 4]);
            },
        ];
        yield [
            ' WHERE (0 = 1)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereIn('phone', []);
            },
        ];
        yield [
            ' WHERE (phone <> ALL (ARRAY[1, 2, 4]))',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereNotIn('phone', [1, 2, 4]);
            },
        ];
        yield [
            ' WHERE (1 = 1)',
            function (Blueprint $table) {
                $table->uniquePartial('name')->whereNotIn('phone', []);
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
