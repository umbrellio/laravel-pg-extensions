<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Schema\Grammars;

use Umbrellio\Postgres\Tests\TestCase;
use Umbrellio\Postgres\Tests\Unit\Helpers\BlueprintAssertions;

class GrammarTest extends TestCase
{
    use BlueprintAssertions;

    private const TABLE = 'test_table';

    protected function setUp(): void
    {
        parent::setUp();

        $this->initializeMock(static::TABLE);
    }

    /** @test */
    public function addingGinIndex(): void
    {
        $this->blueprint->gin('foo');
        $this->assertRegExpSql('/CREATE INDEX test_table_foo_gin ON (public.)?"test_table" USING GIN\("foo"\)/');
    }

    /** @test */
    public function addingGistIndex(): void
    {
        $this->blueprint->gist('foo');
        $this->assertRegExpSql('/CREATE INDEX test_table_foo_gist ON (public.)?"test_table" USING GIST\("foo"\)/');
    }
}
