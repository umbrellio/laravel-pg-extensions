<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions;

use Illuminate\Support\Fluent;

/**
 * @method CheckDefinition where($column, $operator, $value, $boolean = 'and')
 * @method CheckDefinition whereRaw($sql, $bindings = [], $boolean = 'and')
 * @method CheckDefinition whereColumn($first, $operator, $second, $boolean = 'and')
 * @method CheckDefinition whereIn($column, $values = [], $boolean = 'and', $not = false)
 * @method CheckDefinition whereNotIn($column, $values = [], $boolean = 'and')
 * @method CheckDefinition whereBetween($column, $values, $boolean = 'and', $not = false)
 * @method CheckDefinition whereNotBetween($column, $values, $boolean = 'and')
 * @method CheckDefinition whereNull($column, $boolean = 'and', $not = false)
 * @method CheckDefinition whereNotNull($column, $boolean = 'and')
 */
class CheckDefinition extends Fluent
{
}
