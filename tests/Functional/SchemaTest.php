<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Commands\CreateCommand;

class SchemaTest extends FunctionalTestCase
{
    /** @test */
    public function simpleCreate()
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        $this->assertTrue(Schema::hasTable('test_table'));
        $this->assertSame(['id', 'name'], Schema::getColumnListing('test_table'));
    }

    /** @test */
    public function extendedCreate()
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('test_table2', function (Blueprint $table, CreateCommand $command) {
            $command->like('test_table');
        });

        $this->assertTrue(Schema::hasTable('test_table'));
        $this->assertTrue(Schema::hasTable('test_table2'));

        $this->assertSame(Schema::getColumnListing('test_table'), Schema::getColumnListing('test_table2'));
    }
}
