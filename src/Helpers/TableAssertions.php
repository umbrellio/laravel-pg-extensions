<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Helpers;

use Illuminate\Support\Facades\Schema;

/**
 * @static void assertTrue($condition, string $message = '')
 * @static void assertSame($expected, $actual, string $message = '')
 *
 * @see Assert
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
