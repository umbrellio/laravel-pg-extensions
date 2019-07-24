<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Helpers;

use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\TestCase;

/**
 * @mixin TestCase
 */
trait TableAssertions
{
    protected function assertCompareTables(string $sourceTable, string $destinationTable): void
    {
        $this->assertSame($this->getTableDefinition($sourceTable), $this->getTableDefinition($destinationTable));
    }

    protected function assertSameTable(array $expectedDef, string $table): void
    {
        $definition = $this->getTableDefinition($table);

        $this->assertSame($expectedDef, $definition);
    }

    protected function seeTable(string $table): void
    {
        $this->assertTrue(Schema::hasTable($table));
    }

    private function getTableDefinition(string $table): array
    {
        return Schema::getColumnListing($table);
    }
}
