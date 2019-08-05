<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Helpers;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

/**
 * @mixin TestCase
 */
trait IndexAssertions
{
    protected function seeIndex(string $index): void
    {
        $this->assertNotNull($this->getIndexListing($index));
    }

    protected function assertSameIndex(string $index, string $expectedDef): void
    {
        $definition = $this->getIndexListing($index);

        $this->seeIndex($index);
        $this->assertSame($expectedDef, $definition);
    }

    protected function assertRegExpIndex(string $index, string $expectedDef): void
    {
        $definition = $this->getIndexListing($index);

        $this->seeIndex($index);
        $this->assertRegExp($expectedDef, $definition);
    }
    private function getIndexListing($index): ?string
    {
        $definition = DB::selectOne('SELECT indexdef FROM pg_indexes WHERE indexname = ?', [$index]);

        return $definition ? $definition->indexdef : null;
    }
}
