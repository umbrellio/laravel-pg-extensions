<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\Functional\Helpers\RoutineAssertions;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class CreateRoutineTest extends FunctionalTestCase
{
    use DatabaseTransactions, RoutineAssertions;

    /** @test */
    public function createFunction(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->createFunction('test_function')
                ->security()->definer()
                ->stability()->leakProof(true)
                ->with('test')
                ->window();
        });

        $this->assertTrue(true);
    }
}
