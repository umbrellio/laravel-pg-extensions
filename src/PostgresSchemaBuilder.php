<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Closure;
use Illuminate\Database\Schema\PostgresBuilder;

class PostgresSchemaBuilder extends PostgresBuilder
{
    public function create($table, Closure $callback)
    {
        $this->build(tap($this->createBlueprint($table), function ($blueprint) use ($callback) {
            $create = $blueprint->createExtended();

            $callback($blueprint, $create);
        }));
    }
}
