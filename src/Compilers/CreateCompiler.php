<?php

namespace Umbrellio\Postgres\Compilers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Fluent;

class CreateCompiler
{
    public static function compile(Grammar $grammar, Blueprint $blueprint, array $columns, array $commands = []): string
    {
        $compiledCommand = sprintf('%s table %s %s (%s)',
            $blueprint->temporary ? 'create temporary' : 'create',
            self::beforeTable($commands['ifNotExists']),
            $grammar->wrapTable($blueprint),
            $commands['like']
                ? self::compileLike($grammar, $commands['like'])
                : self::compileColumns($columns)
        );

        return str_replace('  ', ' ', trim($compiledCommand));
    }

    private static function beforeTable(?Fluent $command = null): string
    {
        return $command ? 'if not exists' : '';
    }

    private static function compileLike(Grammar $grammar, Fluent $command): string
    {
        $table = $command->get('table');
        $includingAll = $command->get('includingAll') ? ' including all' : '';
        return "like {$grammar->wrapTable($table)}{$includingAll}";
    }

    private static function compileColumns(array $columns): string
    {
        return implode(', ', $columns);
    }
}
