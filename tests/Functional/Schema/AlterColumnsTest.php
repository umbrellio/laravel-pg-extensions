<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Schema\Blueprint;

use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\Functional\Helpers\ColumnAssertions;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class AlterColumnsTest extends FunctionalTestCase
{
    use DatabaseTransactions, ColumnAssertions;

    /** @test */
    public function alterTableUsingByDefault(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->string('code')->default('1');
        });

        $this->assertDefaultOnColumn('test_table', 'code', "'1'::character varying");

        Schema::table('test_table', function (Blueprint $table) {
            $table->integer('code')->default(null)->change();
        });

        $this->assertTypeColumn('test_table', 'code', 'integer');
        $this->assertDefaultOnColumn('test_table', 'code');
    }

    /** @test */
    public function alterTableUsingWithExpression(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('number')->default('1')->nullable();
        });

        $this->assertDefaultOnColumn('test_table', 'number', '1');

        DB::table('test_table')->insert([['id' => 1]]);

        $this->assertDatabaseHas('test_table', ['id' => 1]);
        $this->assertTypeColumn('test_table', 'number', 'integer');

        Schema::table('test_table', function (Blueprint $table) {
            $table->string('number')
                ->using("('[' || number || ']')::character varying")
                ->change();
        });

        $this->assertDefaultOnColumn('test_table', 'number', "'1'::character varying");
        $this->assertTypeColumn('test_table', 'number', 'string');
        $this->assertDatabaseHas('test_table', [
            'id' => 1,
            'number' => '[1]',
        ]);
    }

    /** @test */
    public function alterTableSetDefault(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->integer('code')->nullable();
        });

        $this->assertTypeColumn('test_table', 'code', 'integer');
        $this->assertDefaultOnColumn('test_table', 'code');

        Schema::table('test_table', function (Blueprint $table) {
            $table->string('code')->default('test_string')->change();
        });

        $this->assertTypeColumn('test_table', 'code', 'string');
        $this->assertDefaultOnColumn('test_table', 'code', "'test_string'::character varying");
    }

    /** @test */
    public function alterTableChangeDefault(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->string('description')->default('default1');
        });

        $this->assertDefaultOnColumn('test_table', 'description', "'default1'::character varying");

        Schema::table('test_table', function (Blueprint $table) {
            $table->text('description')->default('default2')->change();
        });

        $this->assertDefaultOnColumn('test_table', 'description', "'default2'::text");
    }

    /** @test */
    public function alterTableDropDefault(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->string('description')->default('default_value');
        });

        $this->assertDefaultOnColumn('test_table', 'description', "'default_value'::character varying");

        Schema::table('test_table', function (Blueprint $table) {
            $table->string('description')->nullable()->default(null)->change();
        });

        $this->assertDefaultOnColumn('test_table', 'description');
    }

    /** @test */
    public function alterTableSetDefaultExpression(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        $this->assertDefaultOnColumn('test_table', 'code');

        Schema::table('test_table', function (Blueprint $table) {
            $table->string('code')->default(new Expression("''::character varying"))->change();
        });

        $this->assertDefaultOnColumn('test_table', 'code', "''::character varying");
    }
}
