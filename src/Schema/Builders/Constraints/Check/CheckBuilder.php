<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Builders\Constraints\Check;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Builders\WhereBuilderTrait;

class CheckBuilder extends Fluent
{
    use WhereBuilderTrait;
}
