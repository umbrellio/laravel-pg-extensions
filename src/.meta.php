<?php

namespace Illuminate\Database\Schema {

    use Illuminate\Support\Fluent;
    use Umbrellio\Postgres\Schema\Definitions\AttachPartitionDefinition;
    use Umbrellio\Postgres\Schema\Definitions\LikeDefinition;
    use Umbrellio\Postgres\Schema\Definitions\UniqueDefinition;

    /**
     * @method AttachPartitionDefinition attachPartition(string $partition)
     * @method void detachPartition(string $partition)
     * @method LikeDefinition like(string $table)
     * @method Fluent ifNotExists()
     * @method UniqueDefinition uniquePartial($columns, ?string $index = null, ?string $algorithm = null)
     * @method ColumnDefinition dateRange(string $column)
     */
    class Blueprint
    {
    }
}
