<?php

namespace Umbrellio\Postgres\Schema\Definitions;

use Codeception\Util\Annotation;
use Illuminate\Support\Fluent;
use phpDocumentor\Reflection\DocBlock;
use ReflectionClass;

class UniqueDefinition extends Fluent
{
    /**
     * @param string $sql
     * @param array $bindings
     * @param string $boolean
     * @return UniqueWhereDefinition
     */
    public function whereRaw($sql, $bindings = [], $boolean = 'and')
    {
        return $this->createCommand( "{$boolean}WhereRaw", compact('sql', 'bindings'));
    }

    /**
     * @param string $column
     * @param null $operator
     * @param null $value
     * @param string $boolean
     * @return UniqueWhereDefinition
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        return $this->createCommand("{$boolean}Where", compact('column', 'operator', 'value'));
    }

    /**
     * @param $first
     * @param mixed|string|null $operator
     * @param mixed|string|null $second
     * @param string $boolean
     * @return UniqueWhereDefinition
     */
    public function whereColumn($first, $operator = null, $second = null, $boolean = 'and')
    {
        return $this->createCommand("{$boolean}WhereColumn", compact('first', 'operator', 'second'));
    }

    /**
     * @param string $column
     * @param array $values
     * @param string $boolean
     * @param bool $not
     * @return UniqueWhereDefinition
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotIn' : 'In';
        return $this->createCommand("{$boolean}Where{$type}", compact('column', 'values', 'not'));
    }

    /**
     * @param string $column
     * @param array $values
     * @param string $boolean
     * @return UniqueWhereDefinition
     */
    public function whereNotIn($column, $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
//        return $this->createCommand("{$boolean}WhereNotIn", compact('column', 'values'));
    }

    /**
     * @param string $column
     * @param string $boolean
     * @param bool $not
     * @return UniqueWhereDefinition
     */
    public function whereNull($column, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotNull' : 'Null';
        return $this->createCommand("{$boolean}Where{$type}", compact('column', 'boolean'));
    }

    /**
     * @param string $column
     * @param array $values
     * @param string $boolean
     * @param bool $not
     * @return UniqueWhereDefinition
     */
    public function whereBetween($column, $values, $boolean = 'and', $not = false)
    {
        return $this->createCommand("{$boolean}WhereBetween", compact('column', 'values', 'not'));
    }

    /**
     * @param string $column
     * @param array $values
     * @param string $boolean
     * @return UniqueWhereDefinition
     */
    public function whereNotBetween($column, $values, $boolean = 'and')
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }

    /**
     * @param string $column
     * @param string $boolean
     * @return UniqueWhereDefinition
     */
    public function whereNotNull($column, $boolean = 'and')
    {
        return $this->whereNull($column, $boolean, true);
    }

    protected function createCommand(string $name, array $parameters = []): UniqueWhereDefinition
    {
        $command = new UniqueWhereDefinition();
        $this->attributes['constraints'] = call_user_func_array([$command, $name], $parameters);
        return $command;
    }
}
