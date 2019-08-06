<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions;

/**
 * @method static where($column, $operator, $value, $boolean = 'and')
 * @method static whereRaw($sql, $bindings = [], $boolean = 'and')
 * @method static whereColumn($first, $operator, $second, $boolean = 'and')
 * @method static whereIn($column, $values = [], $boolean = 'and', $not = false)
 * @method static whereNotIn($column, $values = [], $boolean = 'and')
 * @method static whereBetween($column, $values, $boolean = 'and', $not = false)
 * @method static whereNotBetween($column, $values, $boolean = 'and')
 * @method static whereNull($column, $boolean = 'and', $not = false)
 * @method static whereNotNull($column, $boolean = 'and')
 */
abstract class AbstractWhereDefinition
{
}
