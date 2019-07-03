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
     * @return Blueprint|\Illuminate\Database\Schema\Blueprint
     */
    protected function createBlueprint($table, Closure $callback = null)
    {
        return new Blueprint($table, $callback);
    }

    public function createView(string $view, string $select, $materialize = false): void
    {
        $blueprint = $this->createBlueprint($view);
        $blueprint->createView($view, $select, $materialize);
        $this->build($blueprint);
    }

    public function dropView(string $view): void
    {
        $blueprint = $this->createBlueprint($view);
        $blueprint->dropView($view);
        $this->build($blueprint);
    }
}
