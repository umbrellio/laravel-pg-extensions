<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema;

use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Support\Fluent;
use Illuminate\Validation\Rules\Unique;
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
        return $this->addExtendedCommand(
            AttachPartitionDefinition::class, 
            'attachPartition',
            compact('partition')
        );
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
        return $this->addExtendedCommand(LikeDefinition::class, 'like', compact('table'));
    }

    public function ifNotExists(): Fluent
    {
        return $this->addCommand('ifNotExists');
    }

    /**
     * @param string|array $columns
     * @param string|null $index
     * @param string|null $algorithm
     * @return UniqueDefinition
     */
    public function uniquePartial($columns, $index = null, $algorithm = null)
    {
        $columns = (array) $columns;

        // If no name was specified for this index, we will create one using a basic
        // convention of the table name, followed by the columns, followed by an
        // index type, such as primary or index, which makes the index unique.
        $index = $index ?: $this->createIndexName('unique', $columns);
        
        return $this->addExtendedCommand(
            UniqueDefinition::class,
            'uniquePartial',
            compact('columns', 'index', 'algorithm')
        );
    }

    /**
     * Add a new extented command to the blueprint.
     *
     * @param  string  $fluent
     * @param  string  $name
     * @param  array  $parameters
     * @return \Illuminate\Support\Fluent
     */
    protected function addExtendedCommand($fluent, $name, array $parameters = [])
    {
        $this->commands[] = $command = $this->createExtendedCommand($fluent, $name, $parameters);
        return $command;
    }

    /**
     * Create a new Extended Fluent command.
     *
     * @param  string  $fluent
     * @param  string  $name
     * @param  array  $parameters
     * @return \Illuminate\Support\Fluent
     */
    protected function createExtendedCommand($fluent, $name, array $parameters = [])
    {
        return new $fluent(array_merge(compact('name'), $parameters));
    }
}
