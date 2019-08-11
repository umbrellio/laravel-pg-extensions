<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions\Functions;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Definitions\FunctionDefinition;

/**
 * @method FunctionDefinition invoker(?bool $external = null)
 * @method FunctionDefinition definer(?bool $external = null)
 */
class SecurityDefinition extends Fluent
{
}
