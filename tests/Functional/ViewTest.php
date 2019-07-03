<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;

class ViewTest extends FunctionalTestCase
{
    /** @test */
    public function createBlueprintView(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->createView(
                'test_view',
                'select * from test_table where name is not null'
            )->materialize();
        });

        $this->assertTrue(Schema::hasTable('test_view'));

        Schema::table('test_table', function (Blueprint $table) {
            $table->dropView('test_view');
        });

        $this->assertFalse(Schema::hasTable('test_view'));
    }

    /** @test */
    public function createFacadeView(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::createView('test_view', 'select * from test_table where name is not null', false);

        $this->assertTrue(Schema::hasTable('test_view'));

        Schema::dropView('test_view');

        $this->assertFalse(Schema::hasTable('test_view'));
    }
}
