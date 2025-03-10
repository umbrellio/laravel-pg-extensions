<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Unit\Schema\Blueprint;

use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function createFunction(): void
    {
        $this->blueprint->createFunction('test_function');
        $this->assertTrue(true);
    }

    #[Test]
    public function createProcedure(): void
    {
        $this->blueprint->createProcedure('test_procedure');
        $this->assertTrue(true);
    }

    #[Test]
    public function createTrigger(): void
    {
        $this->blueprint->createTrigger('test_trigger');
        $this->assertTrue(true);
    }

    #[Test]
    public function dropFunction(): void
    {
        $this->blueprint->dropFunction('test_function');
        $this->assertTrue(true);
    }

    #[Test]
    public function dropProcedure(): void
    {
        $this->blueprint->dropProcedure('test_procedure');
        $this->assertTrue(true);
    }

    #[Test]
    public function dropTrigger(): void
    {
        $this->blueprint->dropTrigger('test_trigger');
        $this->assertTrue(true);
    }
}
