<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Helpers;

use Illuminate\Support\Facades\Schema;

/**
 * @static void assertSame($expected, $actual, string $message = '')
 * @static void assertTrue($condition, string $message = '')
 * @static void assertFalse($condition, string $message = '')
 *
 * @see Assert
 */
trait ViewAssertions
{
    protected function assertSameView(string $expectedDef, string $view): void
    {
        $definition = $this->getViewDefinition($view);

        $this->assertSame($expectedDef, $definition);
    }

    protected function seeView(string $view): void
    {
        $this->assertTrue(Schema::hasView($view));
    }

    protected function notSeeView(string $view): void
    {
        $this->assertFalse(Schema::hasView($view));
    }

    private function getViewDefinition(string $view): string
    {
        return preg_replace(
            "#\s+#",
            ' ',
            strtolower(trim(str_replace("\n", ' ', Schema::getViewDefinition($view))))
        );
    }
}
