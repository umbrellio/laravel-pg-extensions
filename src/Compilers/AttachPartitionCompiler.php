<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Compilers;

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Fluent;
use InvalidArgumentException;

class AttachPartitionCompiler
{
    public static function compile(Grammar $grammar, Blueprint $blueprint, Fluent $command): string
    {
        return sprintf('alter table %s attach partition %s %s',
            $grammar->wrapTable($blueprint),
            $command->get('partition'),
            self::compileForValues($command)
        );
    }

    private static function compileForValues(Fluent $command): string
    {
        if ($range = $command->get('range')) {
            $from = self::formatValue($range['from']);
            $to = self::formatValue($range['to']);
            return "for values from ({$from}) to ({$to})";
        }

        throw new InvalidArgumentException('Not set "for values" for attachPartition');
    }

    private static function formatValue($date)
    {
        if ($date instanceof Carbon) {
            return "'{$date->toDateTimeString()}'";
        }

        return "'${date}'";
    }
}
