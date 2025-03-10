<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\Functional\Helpers\RoutineAssertions;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class CreateRoutineTest extends FunctionalTestCase
{
    use DatabaseTransactions;

    use RoutineAssertions;

    #[Test]
    public function createFunction(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->createFunction('test_function');
            $table->createProcedure('test_function');
            $table->createTrigger('test_function');
        });

        $this->assertTrue(true);

        Schema::table('test_table', function (Blueprint $table) {
            $table->dropFunction('test_function');
            $table->dropProcedure('test_function');
            $table->dropTrigger('test_function');
        });
    }
}
