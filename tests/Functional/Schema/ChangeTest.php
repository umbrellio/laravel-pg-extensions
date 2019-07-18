<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Schema;

use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class ChangeTest extends FunctionalTestCase
{
    use RefreshDatabase;

//    /** @test */
//    public function schemaChanges(): void
//    {
//        Schema::create('test_table', function (Blueprint $table) {
//            $table->integer('id')->default(1);
//            $table->string('name')->nullable()->default(new Expression('NULL'));
//            $table->binary('binaries');
//            $table->string('email')->unique();
//            $table->tinyInteger('phone')->unsigned();
//            $table->boolean('enabled')->default(0)->comment('Enabled');
//        });
//
//        $this->assertTrue(Schema::hasTable('test_table'));
//        $this->assertSame(
//            ['id', 'name', 'binaries', 'email', 'phone', 'enabled'],
//            Schema::getColumnListing('test_table')
//        );
//
//        Schema::table('test_table', function (Blueprint $table) {
//            $table->integer('id')->primary()->change();
//            $table->string('email')->nullable()->default(new Expression('NULL'))->change();
//            $table->string('binaries')->nullable()->change();
//            $table->string('test1');
//            $table->string('test2')->comment('test_comment');
//            $table->string('dump1')->nullable();
//            $table->dropColumn(['dump1']);
//        });
//
//        $this->assertSame(
//            ['id', 'name', 'binaries', 'email', 'phone', 'enabled', 'test1', 'test2'],
//            Schema::getColumnListing('test_table')
//        );
//
//        Schema::table('test_table', function (Blueprint $table) {
//            $table->integer('id')->autoIncrement()->change();
//            $table->string('email')->nullable()->default('player@example.om')->change();
//            $table->renameColumn('binaries', 'code');
//            $table->dropColumn(['phone', 'enabled']);
//            $table->binary('test1')->nullable()->using('test1::bytea')->change();
//            $table->string('test2')->comment('new_comment')->change();
//        });
//
//        $this->assertSame(['id', 'name', 'code', 'email', 'test1', 'test2'], Schema::getColumnListing('test_table'));
//
//        Schema::table('test_table', function (Blueprint $table) {
//            $table->dropPrimary(['id']);
//            $table->string('name', 100)->change();
//            $table->integer('id')->change();
//            $table->rename('some_table');
//        });
//
//        $this->assertTrue(Schema::hasTable('some_table'));
//
//        Schema::table('some_table', function (Blueprint $table) {
//            $table->drop();
//        });
//
//        $this->assertFalse(Schema::hasTable('some_table'));
//    }
}
