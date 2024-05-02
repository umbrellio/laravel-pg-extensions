<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Schema;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Umbrellio\Postgres\Helpers\ColumnAssertions;
use Umbrellio\Postgres\Helpers\TableAssertions;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class CreateTableTest extends FunctionalTestCase
{
    use DatabaseTransactions;

    use TableAssertions;

    use ColumnAssertions;

    #[Test]
    public function createSimple(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('field_comment')
                ->comment('test');
            $table->integer('field_default')
                ->default(123);
        });

        $this->seeTable('test_table');
    }

    #[Test]
    public function columnAssertions(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('field_comment')
                ->comment('test');
            $table->integer('field_default')
                ->default(123);
        });

        $this->assertSameTable(['id', 'name', 'field_comment', 'field_default'], 'test_table');

        $this->assertPostgresTypeColumn('test_table', 'id', 'integer');
        $this->assertLaravelTypeColumn('test_table', 'name', 'varchar');
        $this->assertPostgresTypeColumn('test_table', 'name', 'character varying');

        $this->assertDefaultOnColumn('test_table', 'field_default', '123');
        $this->assertCommentOnColumn('test_table', 'field_comment', 'test');

        $this->assertDefaultOnColumn('test_table', 'name');
        $this->assertCommentOnColumn('test_table', 'name');
    }

    public function createViaLike(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('test_table2', function (Blueprint $table) {
            $table->like('test_table');
        });

        $this->seeTable('test_table');
        $this->seeTable('test_table2');
        $this->assertCompareTables('test_table', 'test_table2');
    }

    public function createViaLikeIncludingAll(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')
                ->unique();
        });

        Schema::create('test_table2', function (Blueprint $table) {
            $table->like('test_table')
                ->includingAll();
            $table->ifNotExists();
        });

        $this->seeTable('test_table');
        $this->seeTable('test_table2');
        $this->assertCompareTables('test_table', 'test_table2');
    }
}
