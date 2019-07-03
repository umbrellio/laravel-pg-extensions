<?php

namespace Illuminate\Database\Schema {

    use Illuminate\Support\Fluent;
    use Umbrellio\Postgres\Schema\Definitions\AttachPartitionDefinition;
    use Umbrellio\Postgres\Schema\Definitions\LikeDefinition;
    use Umbrellio\Postgres\Schema\Definitions\ViewDefinition;

    /**
     * @method AttachPartitionDefinition attachPartition(string $partition)
     * @method void detachPartition(string $partition)
     * @method LikeDefinition like(string $table)
     * @method Fluent ifNotExists()
     * @method ViewDefinition createView(string $view, string $select, bool $materialize = false)
     * @method Fluent dropView(string $view)
     */
    class Blueprint
    {
    }
}
