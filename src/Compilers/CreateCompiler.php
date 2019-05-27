<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Compilers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Fluent;

class CreateCompiler
{
    public static function compile(Grammar $grammar, Blueprint $blueprint, Fluent $command, array $columns): string
    {
        $compiledCommand = sprintf('%s table %s %s %s',
            $blueprint->temporary ? 'create temporary' : 'create',
            self::beforeTable($grammar, $blueprint, $command),
            $grammar->wrapTable($blueprint),
            self::afterTable($grammar, $blueprint, $command, $columns)
        );

        return str_replace('  ', ' ', trim($compiledCommand));
    }

    private static function beforeTable(Grammar $grammar, Blueprint $blueprint, Fluent $command): string
    {
        return $command->get('ifNotExists') ? 'if not exists' : '';
    }

    private static function afterTable(Grammar $grammar, Blueprint $blueprint, Fluent $command, array $columns): string
    {
        if ($command->get('like')) {
            return self::compileLike($grammar, $blueprint, $command);
        }

        return self::compileColumns($columns);
    }

    private static function compileLike(Grammar $grammar, Blueprint $blueprint, Fluent $command): string
    {
        $table = $command->get('like');

        if (!$table) {
            return '';
        }

        $includingAll = $command->get('includingAll', false) ? 'including all' : '';

        return "(like {$grammar->wrapTable($table)} {$includingAll})";
    }

    private static function compileColumns(array $columns): string
    {
        return '(' . implode(', ', $columns) . ')';
    }
}
