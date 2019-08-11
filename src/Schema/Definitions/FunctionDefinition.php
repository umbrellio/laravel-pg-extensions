<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Definitions\Functions\ArgumentDefinition;
use Umbrellio\Postgres\Schema\Definitions\Functions\ExecutionDefinition;
use Umbrellio\Postgres\Schema\Definitions\Functions\SecurityDefinition;
use Umbrellio\Postgres\Schema\Definitions\Functions\SetDefinition;
use Umbrellio\Postgres\Schema\Definitions\Functions\StabilityDefinition;

/**
 * @method FunctionDefinition replace()
 * @method FunctionDefinition language(string $language = 'plpgsql')
 * @method FunctionDefinition transform(?string $forType = null)
 * @method FunctionDefinition window()
 * @method ArgumentDefinition arg(?string $name = null)
 * @method FunctionDefinition declare(string $var, string $type)
 * @method FunctionDefinition body(string $statement)
 * @method StabilityDefinition stability()
 * @method SecurityDefinition security()
 * @method ExecutionDefinition execution()
 * @method FunctionDefinition cost()
 * @method FunctionDefinition rows()
 * @method SetDefinition set(string $configurationParameter)
 * @method FunctionDefinition retType(string $type)
 * @method FunctionDefinition retTable(array $columns)
 * @method FunctionDefinition as(string $alias)
 * @method FunctionDefinition with(string $attribute)
 */
class FunctionDefinition extends Fluent
{
}
