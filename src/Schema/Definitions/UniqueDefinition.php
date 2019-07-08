<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions;

use Illuminate\Support\Fluent;

/**
 * @method UniqueDefinition where($column, $operator, $value, $boolean = 'and')
 * @method UniqueDefinition whereRaw($sql, $bindings = [], $boolean = 'and')
 * @method UniqueDefinition whereColumn($first, $operator, $second, $boolean = 'and')
 * @method UniqueDefinition whereIn($column, $values = [], $boolean = 'and', $not = false)
 * @method UniqueDefinition whereNotIn($column, $values = [], $boolean = 'and')
 * @method UniqueDefinition whereBetween($column, $values, $boolean = 'and', $not = false)
 * @method UniqueDefinition whereNotBetween($column, $values, $boolean = 'and')
 * @method UniqueDefinition whereNull($column, $boolean = 'and', $not = false)
 * @method UniqueDefinition whereNotNull($column, $boolean = 'and')
 */
class UniqueDefinition extends Fluent
{
}
