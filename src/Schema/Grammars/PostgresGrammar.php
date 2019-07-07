<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Grammars;

use Illuminate\Database\Schema\Grammars\PostgresGrammar as BasePostgresGrammar;
use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Compilers\UniqueWhereCompiler;
use Umbrellio\Postgres\Compilers\AttachPartitionCompiler;
use Umbrellio\Postgres\Compilers\CreateCompiler;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Definitions\UniqueDefinition;
use Umbrellio\Postgres\Schema\Definitions\UniqueWhereDefinition;

class PostgresGrammar extends BasePostgresGrammar
{
    /**
     * @param Blueprint|\Illuminate\Database\Schema\Blueprint $blueprint
     * @param Fluent $command
     * @return string
     */
    public function compileCreate($blueprint, Fluent $command): string
    {
        $like = $this->getCommandByName($blueprint, 'like');
        $ifNotExists = $this->getCommandByName($blueprint, 'ifNotExists');

        return CreateCompiler::compile(
            $this,
            $blueprint,
            $this->getColumns($blueprint),
            compact('like', 'ifNotExists')
        );
    }

    /**
     * @param Blueprint|\Illuminate\Database\Schema\Blueprint $blueprint
     * @param Fluent $command
     * @return string
     */
    public function compileAttachPartition($blueprint, Fluent $command): string
    {
        return AttachPartitionCompiler::compile($this, $blueprint, $command);
    }

    /**
     * @param Blueprint|\Illuminate\Database\Schema\Blueprint $blueprint
     * @param Fluent $command
     * @return string
     */
    public function compileDetachPartition($blueprint, Fluent $command): string
    {
        return sprintf('alter table %s detach partition %s',
            $this->wrapTable($blueprint),
            $command->get('partition')
        );
    }

    /**
     * @param Blueprint|\Illuminate\Database\Schema\Blueprint $blueprint
     * @param UniqueDefinition $command
     * @return string
     */
    public function compileUniquePartial($blueprint, UniqueDefinition $command): string
    {
        $sql = $this->compileUnique($blueprint, $command);
        if ($command->get('constraints') instanceof UniqueWhereDefinition) {
            $sql .= ' WHERE ' . UniqueWhereCompiler::compile($this, $blueprint, $command);
        }
        return $sql;
    }
}
