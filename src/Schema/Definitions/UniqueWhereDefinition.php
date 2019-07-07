<?php

namespace Umbrellio\Postgres\Schema\Definitions;

use Illuminate\Support\Fluent;

class UniqueWhereDefinition extends Fluent
{
    /**
     * @param string $sql
     * @param array $bindings
     * @param string $boolean
     * @return UniqueWhereDefinition
     */
    public function whereRaw($sql, $bindings = [], $boolean = 'and')
    {
        return $this->compileWhere('raw', $boolean, compact('sql', 'bindings'));
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
        return $this->compileWhere('Basic', $boolean, compact('column', 'operator', 'value'));
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
        return $this->compileWhere('Column', $boolean, compact('first', 'operator', 'second'));
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
        return $this->compileWhere( $not ? 'NotIn' : 'In', $boolean, compact('column', 'values'));
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
    }

    /**
     * @param string $column
     * @param string $boolean
     * @param bool $not
     * @return UniqueWhereDefinition
     */
    public function whereNull($column, $boolean = 'and', $not = false)
    {
        return $this->compileWhere($not ? 'NotNull' : 'Null', $boolean, compact('column'));
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
        return $this->compileWhere('between', $boolean, compact('column', 'values', 'not'));
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

    /**
     * @param string $type
     * @param string $boolean
     * @param array $parameters
     * @return $this
     */
    protected function compileWhere($type, $boolean, $parameters)
    {
        $this->attributes['wheres'][] = array_merge(compact('type', 'boolean'), $parameters);
        return $this;
    }
}
