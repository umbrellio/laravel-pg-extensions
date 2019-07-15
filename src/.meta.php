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
     */
    class Blueprint
    {
    }
}

namespace Umbrellio\Postgres\Schema\Extensions {

    use Umbrellio\Postgres\Schema\Blueprint;
    use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;
    use Umbrellio\Postgres\PostgresConnection;
    use Umbrellio\Postgres\Schema\Builder;

    class AbstractBlueprint extends Blueprint
    {
    }

    class AbstractConnection extends PostgresConnection
    {
    }

    class AbstractGrammar extends PostgresGrammar
    {
    }

    class AbstractBuilder extends Builder
    {
    }
}
