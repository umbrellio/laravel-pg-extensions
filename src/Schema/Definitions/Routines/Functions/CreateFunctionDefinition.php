<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions\Routines\Functions;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Definitions\Routines\RoutineDefinition;

/**
 * @mixin RoutineDefinition
 *
 * @method FunctionArgumentDefinition arg(?string $name = null)
 * @method self retType(string $type)
 * @method self retTable(array $columns)
 * @method self window()
 * @method StabilityDefinition stability()
 * @method ExecutionDefinition execution()
 * @method FunctionSecurityDefinition security()
 * @method self cost()
 * @method self rows()
 * @method FunctionSetDefinition set(string $configurationParameter)
 * @method self with(string $attribute)
 */
class CreateFunctionDefinition extends Fluent
{
}
