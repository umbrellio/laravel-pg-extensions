<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Schema;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class SchemaTest extends FunctionalTestCase
{
    use RefreshDatabase;

    /** @test */
    public function create(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        $this->assertTrue(Schema::hasTable('test_table'));
        $this->assertSame(['id', 'name'], Schema::getColumnListing('test_table'));
    }

    /** @test */
    public function createLikeSimple(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('test_table2', function (Blueprint $table) {
            $table->like('test_table');
        });

        $this->assertTrue(Schema::hasTable('test_table'));
        $this->assertTrue(Schema::hasTable('test_table2'));

        $this->assertSame(Schema::getColumnListing('test_table'), Schema::getColumnListing('test_table2'));
    }

    /** @test */
    public function createLikeFull(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
        });

        Schema::create('test_table2', function (Blueprint $table) {
            $table->like('test_table')->includingAll();
            $table->ifNotExists();
        });

        $this->assertTrue(Schema::hasTable('test_table'));
        $this->assertTrue(Schema::hasTable('test_table2'));
        $this->assertSame(Schema::getColumnListing('test_table'), Schema::getColumnListing('test_table2'));
    }
}
