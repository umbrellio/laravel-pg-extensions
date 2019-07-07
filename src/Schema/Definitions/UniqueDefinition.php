<?php

namespace Umbrellio\Postgres\Schema\Definitions;

use Codeception\Util\Annotation;
use Illuminate\Support\Fluent;
use phpDocumentor\Reflection\DocBlock;
use ReflectionClass;

/**
 * @method UniqueWhereDefinition where($column, $operator = null, $value = null, $boolean = 'and')
 * @method UniqueWhereDefinition whereRaw($sql, $bindings = [], $boolean = 'and')
 * @method UniqueWhereDefinition whereColumn($first, $operator = null, $second = null, $boolean = 'and')
 * @method UniqueWhereDefinition whereIn($column, $values, $boolean = 'and', $not = false)
 * @method UniqueWhereDefinition whereNotIn($column, $values, $boolean = 'and')
 * @method UniqueWhereDefinition whereBetween($column, $values, $boolean = 'and', $not = false)
 * @method UniqueWhereDefinition whereNotBetween($column, $values, $boolean = 'and')
 * @method UniqueWhereDefinition whereNull($column, $boolean = 'and', $not = false)
 * @method UniqueWhereDefinition whereNotNull($column, $boolean = 'and')
 */
class UniqueDefinition extends Fluent
{
    public function __call($method, $parameters)
    {
        $command = new UniqueWhereDefinition();
        $this->attributes['constraints'] = call_user_func_array([$command, $method], $parameters);
        return $command;
    }
}
