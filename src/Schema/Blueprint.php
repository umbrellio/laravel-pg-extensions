<?php

namespace Umbrellio\Postgres\Schema;

use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Umbrellio\Postgres\Definitions\LikeDefinition;
use Umbrellio\Postgres\Schema\Definitions\AttachPartitionDefinition;
use Illuminate\Support\Fluent;

class Blueprint extends BaseBlueprint
{
    /**
     * @param string $partition
     * @return AttachPartitionDefinition|Fluent
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
     * @param string $table
     * @return LikeDefinition|Fluent
     */
    public function like(string $table)
    {
        return $this->addCommand('like', compact('table'));
    }

    public function ifNotExists(): Fluent
    {
        return $this->addCommand('ifNotExists');
    }
}
