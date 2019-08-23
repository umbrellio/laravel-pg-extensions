<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Helpers\ViewAssertions;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class CreateViewTest extends FunctionalTestCase
{
    use DatabaseTransactions, ViewAssertions;

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

        $this->seeView('test_view');
        $this->assertSameView(
            'select test_table.id, test_table.name from test_table where (test_table.name is not null);',
            'test_view'
        );

        Schema::dropView('test_view');
        $this->notSeeView('test_view');
    }

    /** @test */
    public function createBlueprintView(): void
    {
        Schema::table('test_table', function (Blueprint $table) {
            $table->createView('test_view', 'select * from test_table where name is not null');
        });

        $this->seeView('test_view');
        $this->assertSameView(
           'select test_table.id, test_table.name from test_table where (test_table.name is not null);',
           'test_view'
        );

        Schema::table('users', function (Blueprint $table) {
            $table->dropView('test_view');
        });

        $this->notSeeView('test_view');
    }
}
