<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Schema\Blueprint;

use Mockery;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\TestCase;

class IndexTest extends TestCase
{
    /** @var Mockery\Mock */
    protected $blueprint;

    protected function setUp(): void
    {
        parent::setUp();

        $this->blueprint = Mockery::mock(Blueprint::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    /** @test */
    public function ginIndex()
    {
        $this->blueprint
            ->shouldReceive('indexCommand')
            ->with('gin', 'col', 'myName');
        $this->blueprint->gin('col', 'myName');
    }

    /** @test */
    public function gistIndex()
    {
        $this->blueprint
            ->shouldReceive('indexCommand')
            ->with('gist', 'col', 'myName');
        $this->blueprint->gist('col', 'myName');
    }
}
