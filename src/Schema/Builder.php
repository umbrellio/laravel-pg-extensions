<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema;

use Closure;
use Illuminate\Database\Schema\PostgresBuilder as BasePostgresBuilder;

class Builder extends BasePostgresBuilder
{
    /**
     * @param string $table
     * @param Closure|null $callback
     * @return \Illuminate\Database\Schema\Blueprint|Blueprint
     */
    protected function createBlueprint($table, Closure $callback = null)
    {
        return new Blueprint($table, $callback);
    }
}
