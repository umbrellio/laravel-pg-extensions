<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Schema;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class HasIndexTest extends FunctionalTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function createIndexIfNotExists(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            if (!$table->hasIndex(['name'], true)) {
                $table->unique(['name']);
            }
        });

        $this->assertTrue(Schema::hasTable('test_table'));

        $indexes = $this->getIndexByName('test_table_name_unique');

        Schema::table('test_table', function (Blueprint $table) {
            if (!$table->hasIndex(['name'], true)) {
                $table->unique(['name']);
            }
        });

        $this->assertTrue(isset($indexes->indexdef));
    }

    protected function getIndexByName($name)
    {
        return collect(DB::select("SELECT indexdef FROM pg_indexes WHERE  indexname = '{$name}'"))->first();
    }
}
