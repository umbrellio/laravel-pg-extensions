<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Compilers;

use Illuminate\Database\Schema\Grammars\Grammar;
use Umbrellio\Postgres\Compilers\Traits\WheresBuilder;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Builders\Indexes\Unique\UniqueBuilder;
use Umbrellio\Postgres\Schema\Builders\Indexes\Unique\UniquePartialBuilder;

class UniqueCompiler
{
    use WheresBuilder;

    public static function compile(
        Grammar $grammar,
        Blueprint $blueprint,
        UniqueBuilder $fluent,
        UniquePartialBuilder $command
    ): string {
        $wheres = static::build($grammar, $blueprint, $command);

        return sprintf(
            'CREATE UNIQUE INDEX %s ON %s (%s) WHERE %s',
            $fluent->get('index'),
            $blueprint->getTable(),
            implode(',', (array) $fluent->get('columns')),
            static::removeLeadingBoolean(implode(' ', $wheres))
        );
    }
}
