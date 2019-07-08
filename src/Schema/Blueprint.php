<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema;

use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
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

    private function addExtendedCommand(string $fluent, string $name, array $parameters = [])
    {
        $command = new $fluent(array_merge(compact('name'), $parameters));
        $this->commands[] = $command;
        return $command;
    }
}
