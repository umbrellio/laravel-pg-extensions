<?php

namespace Umbrellio\Postgres\Definitions {

    use Illuminate\Support\Fluent;


    /**
     * @method void range(array $range)
     */
    class AttachedPartition extends Fluent
    {

    }
}

namespace Illuminate\Database\Schema {

    use Umbrellio\Postgres\Definitions\AttachedPartition;

    /**
     * @method AttachedPartition attachPartition(string $partition)
     * @method void detachPartition(string $partition)
     */
    class Blueprint
    {

    }
}
