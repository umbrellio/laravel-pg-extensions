<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;

class ViewTest extends FunctionalTestCase
{
    /** @test */
    public function createFacadeView(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::createView('test_view', 'select * from test_table where name is not null', true);

        $this->assertSame(
            strtolower('select test_table.id,     test_table.name    from test_table   where (test_table.name is not null);'),
            trim(strtolower(str_replace("\n", ' ', Schema::getViewDefinition('test_view'))))
        );

        Schema::dropView('test_view');
        Schema::dropIfExists('test_table');
    }
}
