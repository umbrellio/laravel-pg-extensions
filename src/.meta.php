<?php

namespace Illuminate\Database\Schema {

    use Illuminate\Support\Fluent;
    use Umbrellio\Postgres\Schema\Definitions\AttachPartitionDefinition;
    use Umbrellio\Postgres\Schema\Definitions\LikeDefinition;
    use Umbrellio\Postgres\Schema\Definitions\ViewDefinition;
    use Umbrellio\Postgres\Schema\Definitions\UniqueDefinition;

    /**
     * @method AttachPartitionDefinition attachPartition(string $partition)
     * @method void detachPartition(string $partition)
     * @method LikeDefinition like(string $table)
     * @method Fluent ifNotExists()
     * @method UniqueDefinition uniquePartial($columns, ?string $index = null, ?string $algorithm = null)
     * @method ViewDefinition createView(string $view, string $select, bool $materialize = false)
     * @method Fluent dropView(string $view)
     * @method Fluent gin($columns, ?string $name = null)
     * @method Fluent gist($columns, ?string $name = null)
     * @method ColumnDefinition numeric(string $column, ?int $precision = null, ?int $scale = null):
     */
    class Blueprint
    {
    }
}
