<?php

namespace Umbrellio\Postgres\Schema\Definitions;

use DeepCopy\Reflection\ReflectionHelper;
use Illuminate\Support\Fluent;
use InvalidArgumentException;
use ReflectionClass;
use Reflection;

class UniqueWhereDefinition extends Fluent
{
    /**
     * @param string $sql
     * @param array $bindings
     * @return $this
     */
    public function andWhereRaw($sql, $bindings = [])
    {
        $this->attributes['andWhereRaw'][] = compact('sql', 'bindings');
        return $this;
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @return $this
     */
    public function orWhereRaw($sql, $bindings = [])
    {
        $this->attributes['orWhereRaw'][] = compact('sql', 'bindings');
        return $this;
    }

    /**
     * @param string $column
     * @param mixed|string|null $operator
     * @param mixed|string|null $value
     * @return $this
     */
    public function andWhere($column, $operator = null, $value = null)
    {
        $this->attributes['andWhere'][] = compact('column', 'operator', 'value');
        return $this;
    }

    /**
     * @param string $column
     * @param mixed|string|null $operator
     * @param mixed|string|null $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        $this->attributes['orWhere'][] = compact('column', 'operator', 'value');
        return $this;
    }

    /**
     * @param string $first
     * @param mixed|string|null $operator
     * @param mixed|string|null $second
     * @return $this
     */
    public function andWhereColumn($first, $operator = null, $second = null)
    {
        $this->attributes['andWhereColumn'][] = compact('first', 'operator', 'second');
        return $this;
    }

    /**
     * @param string $first
     * @param mixed|string|null $operator
     * @param mixed|string|null $second
     * @return $this
     */
    public function orWhereColumn($first, $operator = null, $second = null)
    {
        $this->attributes['orWhereColumn'][] = compact('first', 'operator', 'second');
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function andWhereIn($column, $values)
    {
        $this->attributes['andWhereIn'][] = compact('column', 'values');
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function orWhereIn($column, $values)
    {
        $this->attributes['orWhereIn'][] = compact('column', 'values');
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function andWhereNotIn($column, $values)
    {
        $this->attributes['andWhereNotIn'][] = compact('column', 'values');
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function orWhereNotIn($column, $values)
    {
        $this->attributes['orWhereNotIn'][] = compact('column', 'values');
        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function andWhereNull($column)
    {
        $this->attributes['andWhereNull'][] = compact('column');
        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function orWhereNull($column)
    {
        $this->attributes['orWhereNull'][] = compact('column');
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function andWhereBetween($column, $values)
    {
        $this->attributes['andWhereBetween'][] = compact('column', 'values');
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function orWhereBetween($column, $values)
    {
        $this->attributes['orWhereBetween'][] = compact('column', 'values');
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function andWhereNotBetween($column, $values)
    {
        $this->attributes['andWhereNotBetween'][] = compact('column', 'values');
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function orWhereNotBetween($column, $values)
    {
        $this->attributes['orWhereNotBetween'][] = compact('column', 'values');
        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function andWhereNotNull($column)
    {
        $this->attributes['andWhereNotNull'][] = compact('column');
        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function orWhereNotNull($column)
    {
        $this->attributes['orWhereNotNull'][] = compact('column');
        return $this;
    }
}
