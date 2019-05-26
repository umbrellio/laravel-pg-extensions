<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Illuminate\Support\Fluent;

class PostgresSchemaGrammar extends PostgresGrammar
{
    public function compileDetachPartition(Blueprint $blueprint, Fluent $command): string
    {
        return sprintf('alter table %s detach partition %s',
            $this->wrapTable($blueprint),
            $command->get('partition')
        );
    }
}
