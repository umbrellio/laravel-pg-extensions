<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema;

use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Definitions\AttachPartitionDefinition;
use Umbrellio\Postgres\Schema\Definitions\LikeDefinition;
use Umbrellio\Postgres\Schema\Definitions\UniqueDefinition;

class Blueprint extends BaseBlueprint
{
    public function attachPartition(string $partition): AttachPartitionDefinition
    {
        return $this->addExtendedCommand(AttachPartitionDefinition::class, 'attachPartition', compact(
            'partition'
        ));
    }

    public function detachPartition(string $partition): void
    {
        $this->addCommand('detachPartition', compact('partition'));
    }

    public function like(string $table): LikeDefinition
    {
        return $this->addExtendedCommand(LikeDefinition::class, 'like', compact('table'));
    }

    public function ifNotExists(): Fluent
    {
        return $this->addCommand('ifNotExists');
    }

    public function uniquePartial($columns, $index = null, $algorithm = null): UniqueDefinition
    {
        $columns = (array) $columns;

        // If no name was specified for this index, we will create one using a basic
        // convention of the table name, followed by the columns, followed by an
        // index type, such as primary or index, which makes the index unique.
        $index = $index ?: $this->createIndexName('unique', $columns);
        
        return $this->addExtendedCommand(UniqueDefinition::class, 'uniquePartial', compact(
            'columns',
            'index',
            'algorithm'
        ));
    }

    /**
     * @return Fluent|LikeDefinition|AttachPartitionDefinition|UniqueDefinition
     */
    protected function addExtendedCommand(string $fluent, string $name, array $parameters = [])
    {
        $this->commands[] = $command = $this->createExtendedCommand($fluent, $name, $parameters);
        return $command;
    }

    protected function createExtendedCommand($fluent, $name, array $parameters = [])
    {
        return new $fluent(array_merge(compact('name'), $parameters));
    }
}
