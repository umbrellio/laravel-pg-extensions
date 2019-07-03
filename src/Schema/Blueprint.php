<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema;

use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Definitions\AttachPartitionDefinition;
use Umbrellio\Postgres\Schema\Definitions\LikeDefinition;

class Blueprint extends BaseBlueprint
{
    /**
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

    /**
     * @return ViewDefinition|Fluent
     */
    public function createView(string $view, string $select, bool $materialize = false): Fluent
    {
        return $this->addCommand('createView', compact('view', 'select', 'materialize'));
    }

    public function dropView(string $view): Fluent
    {
        return $this->addCommand('dropView', compact('view'));
    }
}
