<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions;

use Illuminate\Support\Fluent;

/**
 * @method ExcludeDefinition where($column, $operator, $value, $boolean = 'and')
 * @method ExcludeDefinition whereRaw($sql, $bindings = [], $boolean = 'and')
 * @method ExcludeDefinition whereColumn($first, $operator, $second, $boolean = 'and')
 * @method ExcludeDefinition whereIn($column, $values = [], $boolean = 'and', $not = false)
 * @method ExcludeDefinition whereNotIn($column, $values = [], $boolean = 'and')
 * @method ExcludeDefinition whereBetween($column, $values, $boolean = 'and', $not = false)
 * @method ExcludeDefinition whereNotBetween($column, $values, $boolean = 'and')
 * @method ExcludeDefinition whereNull($column, $boolean = 'and', $not = false)
 * @method ExcludeDefinition whereNotNull($column, $boolean = 'and')
 * @method ExcludeDefinition method(string $method)
 * @method ExcludeDefinition with(string $storageParameter, $value)
 * @method ExcludeDefinition tableSpace(string $tableSpace)
 * @method ExcludeDefinition using(string $excludeElement, string $operator)
 */
class ExcludeDefinition extends Fluent
{
}
