<?php

namespace Umbrellio\Postgres\Definitions {

    use Illuminate\Support\Fluent;
    use Umbrellio\Postgres\Commands\CreateCommand;


    /**
     * @method void range(array $range)
     */
    class AttachPartitionDefinition extends Fluent
    {

    }

    /**
     * @method CreateCommand includingAll()
     */
    class LikeDefinition extends Fluent
    {

    }
}

namespace Illuminate\Database\Schema {

    use Umbrellio\Postgres\Definitions\AttachPartitionDefinition;

    /**
     * @method AttachPartitionDefinition attachPartition(string $partition)
     * @method void detachPartition(string $partition)
     */
    class Blueprint
    {

    }
}
