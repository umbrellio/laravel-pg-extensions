<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Compilers;

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Support\Fluent;
use InvalidArgumentException;

class UniqueWhereCompiler
{
    public static function compile(Grammar $grammar, Blueprint $blueprint, Fluent $command): string
    {
        dd($blueprint, $command);
    }
}
