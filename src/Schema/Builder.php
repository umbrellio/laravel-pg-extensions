<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema;

use Closure;
use Illuminate\Database\Schema\PostgresBuilder as BasePostgresBuilder;
use Illuminate\Support\Traits\Macroable;

class Builder extends BasePostgresBuilder
{
    use Macroable;

    public $name;

    /**
     * @codeCoverageIgnore
     */
    public function createView(string $view, string $select, $materialize = false): void
    {
        $blueprint = $this->createBlueprint($view);
        $blueprint->createView($view, $select, $materialize);
        $this->build($blueprint);
    }

    /**
     * @codeCoverageIgnore
     */
    public function dropView(string $view): void
    {
        $blueprint = $this->createBlueprint($view);
        $blueprint->dropView($view);
        $this->build($blueprint);
    }

    /**
     * @codeCoverageIgnore
     */
    public function hasView($view): bool
    {
        return count($this->connection->selectFromWriteConnection($this->grammar->compileViewExists(), [
            $this->connection->getConfig()['schema'],
            $this->connection->getTablePrefix() . $view,
        ])) > 0;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getForeignKeys($tableName): array
    {
        return $this->connection->selectFromWriteConnection($this->grammar->compileForeignKeysListing($tableName));
    }

    /**
     * @codeCoverageIgnore
     */
    public function getViewDefinition($view): string
    {
        $results = $this->connection->selectFromWriteConnection($this->grammar->compileViewDefinition(), [
            $this->connection->getConfig()['schema'],
            $this->connection->getTablePrefix() . $view,
        ]);
        return count($results) > 0 ? $results[0]->view_definition : '';
    }

    /**
     * @param string $table
     * @return Blueprint|\Illuminate\Database\Schema\Blueprint
     */
    protected function createBlueprint($table, Closure $callback = null)
    {
        $blueprint = new Blueprint($this->connection, $this->grammar);
        $blueprint->setTable($table);
        $callback($blueprint);
        return $blueprint;
    }
}
