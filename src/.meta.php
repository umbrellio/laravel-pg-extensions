<?php

namespace Illuminate\Database\Schema {

    use Illuminate\Support\Fluent;
    use Umbrellio\Postgres\Schema\Definitions\AttachPartitionDefinition;
    use Umbrellio\Postgres\Schema\Definitions\LikeDefinition;

    /**
     * @method AttachPartitionDefinition attachPartition(string $partition)
     * @method void detachPartition(string $partition)
     * @method LikeDefinition like(string $table)
     * @method Fluent ifNotExists()
     * @method UniqueDefinition uniquePartial(string|array $columns, ?string $index = null, ?string $algorithm = null)
     */
    class Blueprint
    {
    }
}
