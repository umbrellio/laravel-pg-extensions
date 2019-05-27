<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Commands;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Definitions\LikeDefinition;

/**
 * @method LikeDefinition|self like(string $table)
 * @method self ifNotExists()
 */
class CreateCommand extends Fluent
{
}
