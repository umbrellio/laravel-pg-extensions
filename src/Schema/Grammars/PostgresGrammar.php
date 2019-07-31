<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Grammars;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\PostgresGrammar as BasePostgresGrammar;
use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Compilers\AttachPartitionCompiler;
use Umbrellio\Postgres\Compilers\CreateCompiler;
use Umbrellio\Postgres\Compilers\UniqueWhereCompiler;
use Umbrellio\Postgres\Schema\Builders\UniquePartialBuilder;
use Umbrellio\Postgres\Schema\Builders\UniqueWhereBuilder;

class PostgresGrammar extends BasePostgresGrammar
{
    public function compileCreate(Blueprint $blueprint, Fluent $command): string
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

    public function compileUniquePartial(Blueprint $blueprint, UniquePartialBuilder $command): string
    {
        $constraints = $command->get('constraints');
        if ($constraints instanceof UniqueWhereBuilder) {
            return UniqueWhereCompiler::compile($this, $blueprint, $command, $constraints);
        }
        return $this->compileUnique($blueprint, $command);
    }

    public function compileGin(Blueprint $blueprint, Fluent $command): string
    {
        return sprintf(
            'CREATE INDEX %s ON %s USING GIN(%s)',
            $command->index,
            $this->wrapTable($blueprint),
            $this->columnize($command->columns)
        );
    }


    public function compileGist(Blueprint $blueprint, Fluent $command): string
    {
        return sprintf(
            'CREATE INDEX %s ON %s USING GIST(%s)',
            $command->index,
            $this->wrapTable($blueprint),
            $this->columnize($command->columns)
        );
    }

    protected function typeNumeric(Fluent $column): string
    {
        $type = 'numeric';
        if ($column->precision && $column->scale) {
            return "${type}({$column->precision}, {$column->scale})";
        }
        if ($column->precision) {
            return "${type}({$column->precision})";
        }
        return $type;
    }
}
