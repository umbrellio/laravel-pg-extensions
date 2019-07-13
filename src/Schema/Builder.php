<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema;

use Closure;
use Illuminate\Database\Schema\PostgresBuilder as BasePostgresBuilder;
use Illuminate\Support\Traits\Macroable;

class Builder extends BasePostgresBuilder
{
    use Macroable;

    /**
     * @param string $table
     * @param Closure|null $callback
     * @return Blueprint|\Illuminate\Database\Schema\Blueprint
     */
    protected function createBlueprint($table, Closure $callback = null)
    {
        return new Blueprint($table, $callback);
    }
}
