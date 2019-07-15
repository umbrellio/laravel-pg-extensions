<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;

class ViewTest extends FunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('test_table');
        parent::tearDown();
    }

    /** @test */
    public function createFacadeView(): void
    {
        Schema::createView('test_view', 'select * from test_table where name is not null');

        $this->assertSame(
            strtolower(
                'select test_table.id,     test_table.name    from test_table   where (test_table.name is not null);'
            ),
            trim(strtolower(str_replace("\n", ' ', Schema::getViewDefinition('test_view'))))
        );

        Schema::dropView('test_view');
        $this->assertFalse(Schema::hasView('test_view'));
    }

    /** @test */
    public function createBlueprintView(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->createView('test_view', 'select * from users where name is not null');
        });

        $this->assertSame(
            strtolower('select users.id,     users.name    from users   where (users.name is not null);'),
            trim(strtolower(str_replace("\n", ' ', Schema::getViewDefinition('test_view'))))
        );

        Schema::table('users', function (Blueprint $table) {
            $table->dropView('test_view');
        });

        $this->assertFalse(Schema::hasView('test_view'));
    }
}
