<?php

namespace Illuminate\Database\Schema {

    use Illuminate\Support\Fluent;
    use Umbrellio\Postgres\Schema\Definitions\AttachPartitionDefinition;
    use Umbrellio\Postgres\Schema\Definitions\CheckDefinition;
    use Umbrellio\Postgres\Schema\Definitions\ExcludeDefinition;
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
     * @method ColumnDefinition numeric(string $column, ?int $precision = null, ?int $scale = null)
     * @method ColumnDefinition tsrange(string $column)
     * @method ExcludeDefinition exclude($columns, ?string $index = null)
     * @method CheckDefinition check($columns, ?string $index = null)
     */
    class Blueprint
    {
    }

    /**
     * @method ColumnDefinition using($expression)
     */
    class ColumnDefinition
    {
    }
}
