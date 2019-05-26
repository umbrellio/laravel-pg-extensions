<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Compilers\AttachPartitionCompiler;

class PostgresSchemaGrammar extends PostgresGrammar
{
    public function compileAttachPartition(Blueprint $blueprint, Fluent $command): string
    {
        return AttachPartitionCompiler::compile($this, $blueprint, $command);
    }

    public function compileDetachPartition(Blueprint $blueprint, Fluent $command): string
    {
        return sprintf('alter table %s detach partition %s',
            $this->wrapTable($blueprint),
            $command->get('partition')
        );
    }
}
