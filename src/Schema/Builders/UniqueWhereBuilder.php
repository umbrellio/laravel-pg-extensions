<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Builders;

use Illuminate\Support\Fluent;

class UniqueWhereBuilder extends Fluent
{
    public function whereRaw(string $sql, array $bindings = [], string $boolean = 'and'): self
    {
        return $this->compileWhere('Raw', $boolean, compact('sql', 'bindings'));
    }

    public function where(string $column, string $operator, string $value, string $boolean = 'and'): self
    {
        return $this->compileWhere('Basic', $boolean, compact('column', 'operator', 'value'));
    }

    public function whereColumn(string $first, string $operator, string $second, string $boolean = 'and'): self
    {
        return $this->compileWhere('Column', $boolean, compact('first', 'operator', 'second'));
    }

    public function whereIn(string $column, array $values, string $boolean = 'and', bool $not = false): self
    {
        return $this->compileWhere($not ? 'NotIn' : 'In', $boolean, compact('column', 'values'));
    }

    public function whereNotIn(string $column, array $values = [], string $boolean = 'and'): self
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    public function whereNull(string $column, string $boolean = 'and', bool $not = false): self
    {
        return $this->compileWhere($not ? 'NotNull' : 'Null', $boolean, compact('column'));
    }

    public function whereBetween(string $column, array $values = [], string $boolean = 'and', bool $not = false): self
    {
        return $this->compileWhere('Between', $boolean, compact('column', 'values', 'not'));
    }

    public function whereNotBetween(string $column, array $values = [], string $boolean = 'and'): self
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }

    public function whereNotNull(string $column, string $boolean = 'and'): self
    {
        return $this->whereNull($column, $boolean, true);
    }

    protected function compileWhere(string $type, string $boolean, array $parameters = []): self
    {
        $this->attributes['wheres'][] = array_merge(compact('type', 'boolean'), $parameters);
        return $this;
    }
}
