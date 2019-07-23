<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Schema\Grammars;

use Umbrellio\Postgres\Tests\Unit\BlueprintTestCase;

class GrammarTest extends BlueprintTestCase
{
    /** @test */
    public function addingGinIndex()
    {
        $this->blueprint->gin('foo');
        $this->assertRegExpSql('/CREATE INDEX test_table_foo_gin ON (public.)?"test_table" USING GIN\("foo"\)/');
    }

    /** @test */
    public function addingGistIndex()
    {
        $this->blueprint->gist('foo');
        $this->assertRegExpSql('/CREATE INDEX test_table_foo_gist ON (public.)?"test_table" USING GIST\("foo"\)/');
    }
}
