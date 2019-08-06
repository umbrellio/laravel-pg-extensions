<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Builders\Indexes\Unique;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Builders\WhereBuilderTrait;

class UniquePartialBuilder extends Fluent
{
    use WhereBuilderTrait;
}
