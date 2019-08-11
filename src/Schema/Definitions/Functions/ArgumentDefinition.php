<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions\Functions;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Definitions\FunctionDefinition;

/**
 * @method ArgumentDefinition in()
 * @method ArgumentDefinition out()
 * @method ArgumentDefinition inout()
 * @method ArgumentDefinition variadic()
 * @method ArgumentDefinition default()
 * @method ArgumentDefinition equal()
 * @method ArgumentDefinition expression()
 * @method FunctionDefinition type(string $type)
 */
class ArgumentDefinition extends Fluent
{
}
