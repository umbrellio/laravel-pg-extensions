<?php

namespace Umbrellio\Postgres\Schema\Definitions;

use Codeception\Util\Annotation;
use Illuminate\Support\Fluent;
use phpDocumentor\Reflection\DocBlock;
use ReflectionClass;

class UniqueDefinition extends Fluent
{
    public function whereRaw(string $sql, array $bindings = [], string $boolean = 'and'): WhereDefinition
    {
        return $this->createCommand($boolean . 'WhereRaw', compact('sql', 'bindings'));
    }

    public function where(string $column, $operator = null, $value = null, string $boolean = 'and'): WhereDefinition
    {
        return $this->createCommand($boolean . 'Where', compact('column', 'operator', 'value'));
    }

    public function whereColumn(string $first, $operator = null, $second = null, string $boolean = 'and'): WhereDefinition
    {
        return $this->createCommand($boolean . 'WhereColumn', compact('first', 'operator', 'second'));
    }

    public function whereIn(string $column, array $values, string $boolean = 'and', bool $not = false): WhereDefinition
    {
        $command = $boolean . 'Where' . ($not ? 'Not' : '') . 'In';
        return $this->createCommand($command, compact('column', 'values'));
    }

    public function whereNotIn(string $column, array $values, string $boolean = 'and'): WhereDefinition
    {
        return $this->createCommand($boolean . 'WhereNotIn', compact('column', 'values'));
    }

    public function whereNull(string $column, string $boolean = 'and', bool $not = false): WhereDefinition
    {
        $command = $boolean . 'Where' . ($not ? 'Not' : '') . 'Null';
        return $this->createCommand($command, compact('column'));
    }

    public function whereBetween(string $column, array $values, string $boolean = 'and', bool $not = false): WhereDefinition
    {
        $command = $boolean . 'Where' . ($not ? 'Not' : '') . 'Between';
        return $this->createCommand($command, compact('column', 'values'));
    }

    public function whereNotBetween(string $column, array $values, string $boolean = 'and'): WhereDefinition
    {
        return $this->createCommand($boolean . 'WhereNotBetween', compact('column', 'values'));
    }

    public function whereNotNull(string $column, string $boolean = 'and'): WhereDefinition
    {
        return $this->createCommand($boolean . 'WhereNotNull', compact('column'));
    }

    protected function createCommand($name, $parameters): WhereDefinition
    {
        $command = new WhereDefinition();
        $this->attributes['wheres'] = call_user_func_array([$command, $name], $parameters);
        return $command;
    }
}
