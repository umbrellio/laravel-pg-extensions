<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Unit\Schema\Blueprint;

use Umbrellio\Postgres\Tests\TestCase;
use Umbrellio\Postgres\Tests\Unit\Helpers\BlueprintAssertions;

class RoutineTest extends TestCase
{
    use BlueprintAssertions;

    private const TABLE = 'test_table';

    protected function setUp(): void
    {
        parent::setUp();
        $this->initializeMock(static::TABLE);
    }

    /** @test */
    public function createFunction(): void
    {
        $this->assertTrue(true);

        $this->blueprint
            ->createFunction('test_function')
            ->security()->definer()
            ->stability()->leakProof(true)
            ->with('test')
            ->window();
    }
}
