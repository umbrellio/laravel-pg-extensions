<?php

namespace Umbrellio\Postgres\Schema\Definitions;

use DeepCopy\Reflection\ReflectionHelper;
use Illuminate\Support\Fluent;
use InvalidArgumentException;
use ReflectionClass;
use Reflection;

class WhereDefinition extends Fluent
{
    public function andWhereRaw(string $sql, array $bindings = [])
    {
        $this->attributes['andWhereRaw'][] = compact('sql', 'bindings');
        return $this;
    }

    public function orWhereRaw(string $sql, array $bindings = [])
    {
        $this->attributes['orWhereRaw'][] = compact('sql', 'bindings');
        return $this;
    }

    public function andWhere(string $column, $operator = null, $value = null)
    {
        $this->attributes['andWhere'][] = compact('column', 'operator', 'value');
        return $this;
    }

    public function orWhere(string $column, $operator = null, $value = null)
    {
        $this->attributes['orWhere'][] = compact('column', 'operator', 'value');
        return $this;
    }

    public function andWhereColumn(string $first, $operator = null, $second = null)
    {
        $this->attributes['andWhereColumn'][] = compact('first', 'operator', 'second');
        return $this;
    }

    public function orWhereColumn(string $first, $operator = null, $second = null)
    {
        $this->attributes['orWhereColumn'][] = compact('first', 'operator', 'second');
        return $this;
    }

    public function andWhereIn(string $column, array $values)
    {
        $this->attributes['andWhereIn'][] = compact('column', 'values');
        return $this;
    }

    public function orWhereIn(string $column, array $values)
    {
        $this->attributes['orWhereIn'][] = compact('column', 'values');
        return $this;
    }

    public function andWhereNotIn(string $column, array $values)
    {
        $this->attributes['andWhereNotIn'][] = compact('column', 'values');
        return $this;
    }

    public function orWhereNotIn(string $column, array $values)
    {
        $this->attributes['orWhereNotIn'][] = compact('column', 'values');
        return $this;
    }

    public function andWhereNull(string $column)
    {
        $this->attributes['andWhereNull'][] = compact('column');
        return $this;
    }

    public function orWhereNull(string $column)
    {
        $this->attributes['orWhereNull'][] = compact('column');
        return $this;
    }

    public function andWhereBetween(string $column, array $values)
    {
        $this->attributes['andWhereBetween'][] = compact('column', 'values');
        return $this;
    }

    public function orWhereBetween(string $column, array $values)
    {
        $this->attributes['orWhereBetween'][] = compact('column', 'values');
        return $this;
    }
    
    public function andWhereNotBetween(string $column, array $values)
    {
        $this->attributes['andWhereNotBetween'][] = compact('column', 'values');
        return $this;
    }

    public function orWhereNotBetween(string $column, array $values)
    {
        $this->attributes['orWhereNotBetween'][] = compact('column', 'values');
        return $this;
    }
    
    public function andWhereNotNull(string $column)
    {
        $this->attributes['andWhereNotNull'][] = compact('column');
        return $this;
    }

    public function orWhereNotNull(string $column)
    {
        $this->attributes['orWhereNotNull'][] = compact('column');
        return $this;
    }
}
