<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Helpers;

use Illuminate\Support\Facades\Schema;

/**
 * @codeCoverageIgnore
 */
trait ViewAssertions
{
    abstract public static function assertSame($expected, $actual, string $message = ''): void;

    abstract public static function assertTrue($condition, string $message = ''): void;

    abstract public static function assertFalse($condition, string $message = ''): void;

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
