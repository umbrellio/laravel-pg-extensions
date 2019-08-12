<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions\Routines\Functions;

use Illuminate\Support\Fluent;

/**
 * @method self in()
 * @method self out()
 * @method self inout()
 * @method self variadic()
 * @method self default()
 * @method self equal()
 * @method self expression()
 * @method CreateFunctionDefinition type(string $type)
 */
class FunctionArgumentDefinition extends Fluent
{
}
