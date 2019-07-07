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
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'Raw', 'boolean' => 'and'],
            compact('sql', 'bindings')
        );
        return $this;
    }

    /**
     * @param string $sql
     * @param array $bindings
     * @return $this
     */
    public function orWhereRaw($sql, $bindings = [])
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'Raw', 'boolean' => 'or'],
            compact('sql', 'bindings')
        );
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
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'Basic', 'boolean' => 'and'],
            compact('column', 'operator', 'value')
        );
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
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'Basic', 'boolean' => 'or'],
            compact('column', 'operator', 'value')
        );
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
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'Column', 'boolean' => 'and'],
            compact('column', 'operator', 'second')
        );
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
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'Column', 'boolean' => 'or'],
            compact('column', 'operator', 'second')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function andWhereIn($column, $values)
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'In', 'boolean' => 'and'],
            compact('column', 'values')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function orWhereIn($column, $values)
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'In', 'boolean' => 'or'],
            compact('column','values')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function andWhereNotIn($column, $values)
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'NotIn', 'boolean' => 'and'],
            compact('column', 'values')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @return $this
     */
    public function orWhereNotIn($column, $values)
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'NotIn', 'boolean' => 'or'],
            compact('column', 'values')
        );
        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function andWhereNull($column)
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'Null', 'boolean' => 'and'],
            compact('column')
        );
        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function orWhereNull($column)
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'Null', 'boolean' => 'or'],
            compact('column')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @param bool $not
     * @return $this
     */
    public function andWhereBetween($column, $values, $not = false)
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'between', 'boolean' => 'and'],
            compact('column', 'values', 'not')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @param bool $not
     * @return $this
     */
    public function orWhereBetween($column, $values, $not = false)
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'between', 'boolean' => 'or'],
            compact('column', 'values', 'not')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @param bool $not
     * @return $this
     */
    public function andWhereNotBetween($column, $values, $not = true)
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'between', 'boolean' => 'and'],
            compact('column', 'values', 'not')
        );
        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     * @param bool $not
     * @return $this
     */
    public function orWhereNotBetween($column, $values, $not = true)
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'between', 'boolean' => 'or'],
            compact('column', 'values', 'not')
        );
        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function andWhereNotNull($column)
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'NotNull', 'boolean' => 'and'],
            compact('column')
        );
        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function orWhereNotNull($column)
    {
        $this->attributes['wheres'][] = array_merge(
            ['type' => 'NotNull', 'boolean' => 'or'],
            compact('column')
        );
        return $this;
    }
}
