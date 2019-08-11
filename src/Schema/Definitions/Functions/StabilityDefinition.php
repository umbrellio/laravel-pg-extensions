<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions\Functions;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Definitions\FunctionDefinition;

/**
 * @method FunctionDefinition immutable()
 * @method FunctionDefinition stable()
 * @method FunctionDefinition volatile()
 * @method FunctionDefinition leakProof(?bool $not = null)
 */
class StabilityDefinition extends Fluent
{
}
