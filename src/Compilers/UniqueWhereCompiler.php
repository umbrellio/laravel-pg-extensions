<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Compilers;

use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Definitions\UniqueWhereDefinition;

class UniqueWhereCompiler
{
    public static function compile(
        Grammar $grammar,
        Blueprint $blueprint,
        Fluent $fluent,
        UniqueWhereDefinition $command
    ): string {
        $wheres = collect($command->get('wheres'))
            ->map(function ($where) use ($grammar, $blueprint) {
                return implode(' ', [
                    $where['boolean'],
                    '(' . static::{"where{$where['type']}"}($grammar, $blueprint, $where) . ')',
                ]);
            })
            ->all();

        return sprintf(
            'CREATE UNIQUE INDEX %s ON %s (%s) WHERE %s',
            $fluent->get('index'),
            $blueprint->getTable(),
            implode(',', $fluent->get('columns')),
            static::removeLeadingBoolean(implode(' ', $wheres))
        );
    }

    protected static function whereRaw(Grammar $grammar, Blueprint $blueprint, $where = [])
    {
        return call_user_func_array('sprintf', array_merge(
            [str_replace('?', '%s', $where['sql'])],
            static::wrapValues($where['bindings'])
        ));
    }

    protected static function whereBasic(Grammar $grammar, Blueprint $blueprint, $where)
    {
        return implode(' ', [
            $grammar->wrap($where['column']),
            $where['operator'],
            static::wrapValue($where['value']),
        ]);
    }

    protected static function whereColumn(Grammar $grammar, Blueprint $blueprint, $where)
    {
        return implode(' ', [
            $grammar->wrap($where['first']),
            $where['operator'],
            $grammar->wrap($where['second']),
        ]);
    }

    protected static function whereIn(Grammar $grammar, Blueprint $blueprint, $where)
    {
        if (!empty($where['values'])) {
            return implode(' ', [
                $grammar->wrap($where['column']),
                'in',
                '(' . implode(',', static::wrapValues($where['values'])) . ')',
            ]);
        }
        return '0 = 1';
    }

    protected static function whereNotIn(Grammar $grammar, Blueprint $blueprint, $where)
    {
        if (!empty($where['values'])) {
            return implode(' ', [
                $grammar->wrap($where['column']),
                'not in',
                '(' . implode(',', static::wrapValues($where['values'])) . ')',
            ]);
        }
        return '1 = 1';
    }

    protected static function whereNull(Grammar $grammar, Blueprint $blueprint, $where)
    {
        return implode(' ', [$grammar->wrap($where['column']), 'is null']);
    }

    protected static function whereNotNull(Grammar $grammar, Blueprint $blueprint, $where)
    {
        return implode(' ', [$grammar->wrap($where['column']), 'is not null']);
    }

    protected static function whereBetween(Grammar $grammar, Blueprint $blueprint, $where)
    {
        return implode(' ', [
            $grammar->wrap($where['column']),
            $where['not'] ? 'not between' : 'between',
            static::wrapValue(reset($where['values'])),
            'and',
            static::wrapValue(end($where['values'])),
        ]);
    }

    protected static function wrapValues($values = []): array
    {
        return collect($values)->map(function ($value) {
            return static::wrapValue($value);
        })->toArray();
    }

    protected static function wrapValue($value)
    {
        if (is_string($value)) {
            return "'{$value}'";
        }
        return (int) $value;
    }

    protected static function removeLeadingBoolean($value)
    {
        return preg_replace('/and |or /i', '', $value, 1);
    }
}