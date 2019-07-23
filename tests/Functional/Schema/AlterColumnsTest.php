<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Schema\Blueprint;

use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class AlterColumnsTest extends FunctionalTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function alterTableSetSimpleComment(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->string('code')->default('1');
        });
        $this->assertDefaultOnColumn('test_table', 'code', "'1'::character varying");
        Schema::table('test_table', function (Blueprint $table) {
            $table->string('code')->comment('some comment')->change();
        });
        $this->assertCommentOnColumn('test_table', 'code', 'some comment');
        $this->assertDefaultOnColumn('test_table', 'code', "'1'::character varying");
    }

    /** @test */
    public function alterTableJsonSetComment(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->string('json_field');
        });
        $this->assertCommentOnColumn('test_table', 'json_field');
        Schema::table('test_table', function (Blueprint $table) {
            $table->json('json_field')->comment('(DC2Type:json_array)')->change();
        });
        $this->assertCommentOnColumn('test_table', 'json_field', '(DC2Type:json_array)');
    }

    /** @test */
    public function alterTableSetDCComment(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->string('code')->default('1');
        });
        $this->assertDefaultOnColumn('test_table', 'code', "'1'::character varying");
        $this->assertCommentOnColumn('test_table', 'code');
        Schema::table('test_table', function (Blueprint $table) {
            $table->string('code')->comment('(DC2Type:string)')->change();
        });
        $this->assertDefaultOnColumn('test_table', 'code', "'1'::character varying");
        $this->assertCommentOnColumn('test_table', 'code', '(DC2Type:string)');
    }

    /** @test */
    public function alterTableDropDCComment(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->integer('number')->comment('(DC2Type:integer)')->default(1);
        });
        $this->assertCommentOnColumn('test_table', 'number', '(DC2Type:integer)');

        Schema::table('test_table', function (Blueprint $table) {
            $table->integer('number')->comment('test')->change();
        });

        $this->assertCommentOnColumn('test_table', 'number', 'test');
    }

    /** @test */
    public function alterTableChangeSimpleComment(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->integer('number')->comment('(DC2Type:integer)')->default(1);
        });
        $this->assertDefaultOnColumn('test_table', 'number', '1');

        Schema::table('test_table', function (Blueprint $table) {
            $table->string('number')->comment('some comment')->change();
        });
        $this->assertCommentOnColumn('test_table', 'number', 'some comment');
        $this->assertDefaultOnColumn('test_table', 'number', "'1'::character varying");
    }

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

        $this->assertSame('integer', Schema::getColumnType('test_table', 'code'));
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
        $this->assertSame('integer', Schema::getColumnType('test_table', 'number'));

        Schema::table('test_table', function (Blueprint $table) {
            $table->string('number')
                ->using("('[' || number || ']')::character varying")
                ->change();
        });

        $this->assertDefaultOnColumn('test_table', 'number', "'1'::character varying");
        $this->assertSame('string', Schema::getColumnType('test_table', 'number'));
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

        $this->assertSame('integer', Schema::getColumnType('test_table', 'code'));
        $this->assertDefaultOnColumn('test_table', 'code');

        Schema::table('test_table', function (Blueprint $table) {
            $table->string('code')->default('test_string')->change();
        });

        $this->assertSame('string', Schema::getColumnType('test_table', 'code'));
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
            $table->text('description', 25)->default('default2')->change();
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

    /** @test */
    public function alterTableCreateSequence(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->integer('id')->default(1);
        });

        $this->assertDefaultOnColumn('test_table', 'id', '1');

        Schema::table('test_table', function (Blueprint $table) {
            $table->increments('id')->change();
        });

        $this->assertDefaultOnColumn('test_table', 'id', "nextval('test_table_id_seq'::regclass)");
    }

    /** @test */
    public function alterTableDropSequence(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
        });

        $this->assertDefaultOnColumn('test_table', 'id', "nextval('test_table_id_seq'::regclass)");

        Schema::table('test_table', function (Blueprint $table) {
            $table->integer('id')->change();
        });

        $this->assertDefaultOnColumn('test_table', 'id');
    }
}
