<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Schema;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\Functional\Helpers\TableAssertions;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class CreateTableTest extends FunctionalTestCase
{
    use DatabaseTransactions, TableAssertions;

    /** @test */
    public function createSimple(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        $this->seeTable('test_table');
        $this->assertSameTable(['id', 'name'], 'test_table');
    }

    /** @test */
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

    /** @test */
    public function createViaLikeIncludingAll(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
        });

        Schema::create('test_table2', function (Blueprint $table) {
            $table->like('test_table')->includingAll();
            $table->ifNotExists();
        });

        $this->seeTable('test_table');
        $this->seeTable('test_table2');
        $this->assertCompareTables('test_table', 'test_table2');
    }
}
