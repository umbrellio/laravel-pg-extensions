<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema;

use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Builders\UniquePartialBuilder;
use Umbrellio\Postgres\Schema\Definitions\AttachPartitionDefinition;
use Umbrellio\Postgres\Schema\Definitions\LikeDefinition;
use Umbrellio\Postgres\Schema\Definitions\UniqueDefinition;

class Blueprint extends BaseBlueprint
{
    /**
     * @return AttachPartitionDefinition
     */
    public function attachPartition(string $partition)
    {
        return $this->addCommand('attachPartition', compact('partition'));
    }

    public function detachPartition(string $partition): void
    {
        $this->addCommand('detachPartition', compact('partition'));
    }

    /**
     * @return LikeDefinition
     */
    public function like(string $table)
    {
        return $this->addCommand('like', compact('table'));
    }

    public function ifNotExists(): Fluent
    {
        return $this->addCommand('ifNotExists');
    }

    /**
     * @param array|string $columns
     * @return UniqueDefinition
     */
    public function uniquePartial($columns, ?string $index = null, ?string $algorithm = null)
    {
        $columns = (array) $columns;

        $index = $index ?: $this->createIndexName('unique', $columns);

        return $this->addExtendedCommand(
            UniquePartialBuilder::class,
            'uniquePartial',
            compact('columns', 'index', 'algorithm')
        );
    }

    /**
     * Specify an index for the table.
     * @param string|array $columns
     * @return Fluent
     */
    public function gin($columns, ?string $name = null)
    {
        return $this->indexCommand('gin', $columns, $name);
    }

    /**
     * Specify a gist index for the table.
     * @param string|array $columns
     * @return Fluent
     */
    public function gist($columns, ?string $name = null)
    {
        return $this->indexCommand('gist', $columns, $name);
    }

    public function hasIndex($index, bool $unique = false): bool
    {
        if (is_array($index)) {
            $index = $this->createIndexName($unique === false ? 'index' : 'unique', $index);
        }

        return array_key_exists($index, $this->getSchemaManager()->listTableIndexes($this->getTable()));
    }

    /**
     * Almost like 'decimal' type, but can be with variable precision (by default)
     * @param string $column
     * @param int|null $precision
     * @param int|null $scale
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function numeric(string $column, ?int $precision = null, ?int $scale = null)
    {
        return $this->addColumn('numeric', $column, compact('precision', 'scale'));
    }

    protected function addFluentIndexes(): void
    {
        foreach ($this->columns as $column) {
            foreach (['primary', 'unique', 'index', 'gin', 'gist', 'spatialIndex'] as $index) {
                // If the index has been specified on the given column, but is simply
                // equal to "true" (boolean), no name has been specified for this
                // index, so we will simply call the index methods without one.
                if ($column->{$index} === true) {
                    $this->{$index}($column->name);
                    continue 2;
                }
                // If the index has been specified on the column and it is something
                // other than boolean true, we will assume a name was provided on
                // the index specification, and pass in the name to the method.
                elseif (isset($column->{$index})) {
                    $this->{$index}($column->name, $column->{$index});
                    continue 2;
                }
            }
        }
    }

    protected function getSchemaManager()
    {
        return Schema::getConnection()->getDoctrineSchemaManager();
    }

    private function addExtendedCommand(string $fluent, string $name, array $parameters = [])
    {
        $command = new $fluent(array_merge(compact('name'), $parameters));
        $this->commands[] = $command;
        return $command;
    }
}
