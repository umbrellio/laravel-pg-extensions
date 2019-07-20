<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Schema;

use Generator;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class BlueprintTest extends FunctionalTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function alterTableUsing(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
        });

        DB::table('test_table')->insert([
            ['code' => '1'],
        ]);

        Schema::table('test_table', function (Blueprint $table) {
            $table->integer('code')->change();
        });

        $this->assertSame('integer', Schema::getColumnType('test_table', 'code'));

        Schema::table('test_table', function (Blueprint $table) {
            $table->string('code')->using("('[' || code || ']')::character varying")->change();
        });

        $this->assertSame('string', Schema::getColumnType('test_table', 'code'));
        $this->assertSame('[1]', DB::table('test_table')->first()->code);
    }

    /**
     * @test
     */
    public function alterTableDefault(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->default('test_string');
        });

        DB::table('test_table')->insert(['id' => 1]);

        $this->assertSame('test_string', DB::table('test_table')->first()->code);

        Schema::table('test_table', function (Blueprint $table) {
            $table->string('code')->nullable()->default(null)->change();
        });

        DB::table('test_table')->truncate();
        DB::table('test_table')->insert(['id' => 1]);

        $this->assertNull(DB::table('test_table')->first()->code);

        Schema::table('test_table', function (Blueprint $table) {
            $table->string('code')->default(new Expression("''::character varying"))->change();
        });

        DB::table('test_table')->truncate();
        DB::table('test_table')->insert(['id' => 1]);

        $this->assertSame('', DB::table('test_table')->first()->code);

        Schema::table('test_table', function (Blueprint $table) {
            $table->string('code')->default('')->change();
        });

        DB::table('test_table')->truncate();
        DB::table('test_table')->insert(['id' => 1]);

        $this->assertSame('', DB::table('test_table')->first()->code);
    }

    /** @test */
    public function createIndexIfNotExists(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            if (!$table->hasIndex(['name'], true)) {
                $table->unique(['name']);
            }
        });

        $this->assertTrue(Schema::hasTable('test_table'));

        $indexes = $this->getIndexByName('test_table_name_unique');

        Schema::table('test_table', function (Blueprint $table) {
            if (!$table->hasIndex(['name'], true)) {
                $table->unique(['name']);
            }
        });

        $this->assertTrue(isset($indexes->indexdef));
    }

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
        $this->assertRegExp('/' . $this->getDummyIndex() . $expected . '/', $indexes->indexdef);
    }

    /** @test */
    public function createSpecifyIndex(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->string('name')->index('specify_index_name');
        });

        $this->assertTrue(Schema::hasTable('test_table'));

        $this->assertRegExp(
            '/CREATE INDEX specify_index_name ON (public.)?test_table USING btree \(name\)/',
            $this->getIndexByName('specify_index_name')->indexdef
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

    protected function getDummyIndex()
    {
        return 'CREATE UNIQUE INDEX test_table_name_unique ON (public.)?test_table USING btree \(name\)';
    }

    protected function getIndexByName($name)
    {
        return collect(DB::select("SELECT indexdef FROM pg_indexes WHERE  indexname = '{$name}'"))->first();
    }
}
