<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Generator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;

class DefaultValueTest extends FunctionalTestCase
{
    /**
     * @test
     * @dataProvider provideDefaultValues
     */
    public function setDefaultExpression($expected, $column, $callback): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('code');
        });

        DB::statement('create sequence IF NOT EXISTS test_table_id_seq');

        Schema::table('test_table', function (Blueprint $table) use ($callback) {
            $callback($table);
        });

        $this->assertSame($expected, $this->getDefaultValue($column)->column_default);
    }

    public function provideDefaultValues(): Generator
    {
        yield [
            "nextval('test_table_id_seq'::regclass)",
            'id',
            function (Blueprint $table) {
                $table->bigInteger('id')
                    ->default(DB::raw("nextval('test_table_id_seq'::regclass)"))
                    ->change();
            },
        ];
        yield [
            "''::character varying",
            'code',
            function (Blueprint $table) {
                $table->string('code')
                    ->default(DB::raw("''::character varying"))
                    ->change();
            },
        ];
    }

    protected function getDefaultValue(string $column)
    {
        return collect(DB::select(
            'SELECT column_default FROM information_schema.columns WHERE column_name = ? and table_name = ?',
            [$column, 'test_table']
        ))->first();
    }
}
