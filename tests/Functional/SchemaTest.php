<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Umbrellio\Postgres\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Generator;

class SchemaTest extends FunctionalTestCase
{
    /**
     * @dataProvider provideTables
     */
    public function testCreate(string $tableName): void
    {
        Schema::create($tableName, static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        $this->assertTrue(Schema::hasTable($tableName));
        $this->assertSame(['id', 'name'], Schema::getColumnListing($tableName));
    }

    /**
     * @dataProvider provideTables
     */
    public function testCreateLikeSimple(string $tableName1, string $tableName2): void
    {
        Schema::create($tableName1, static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create($tableName2, static function (Blueprint $table) use ($tableName2) {
            $table->like($tableName2);
        });

        $this->assertTrue(Schema::hasTable($tableName1));
        $this->assertTrue(Schema::hasTable($tableName2));

        $this->assertSame(Schema::getColumnListing($tableName1), Schema::getColumnListing($tableName2));
    }

    /**
     * @dataProvider provideTables
     */
    public function testCreateLikeFull(string $tableName1, string $tableName2): void
    {
        Schema::create($tableName1, static function (Blueprint $table) {
            $table->increments('id')->primary();
            $table->string('name')->unique();
        });

        Schema::create($tableName2, static function (Blueprint $table) use ($tableName1) {
            $table->like($tableName1)->includingAll();
            $table->ifNotExists();
        });

        $this->assertTrue(Schema::hasTable($tableName1));
        $this->assertTrue(Schema::hasTable($tableName2));
        $this->assertSame(Schema::getColumnListing($tableName1), Schema::getColumnListing($tableName2));
    }

    public function provideTables(): Generator
    {
        yield ['test_table', 'test_table_like'];
    }
}
