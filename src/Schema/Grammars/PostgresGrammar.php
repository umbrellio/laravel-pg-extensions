<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Grammars;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\PostgresGrammar as BasePostgresGrammar;
use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Compilers\AttachPartitionCompiler;
use Umbrellio\Postgres\Compilers\CheckCompiler;
use Umbrellio\Postgres\Compilers\CreateCompiler;
use Umbrellio\Postgres\Compilers\ExcludeCompiler;
use Umbrellio\Postgres\Compilers\UniqueCompiler;
use Umbrellio\Postgres\Schema\Builders\Constraints\Check\CheckBuilder;
use Umbrellio\Postgres\Schema\Builders\Constraints\Exclude\ExcludeBuilder;
use Umbrellio\Postgres\Schema\Builders\Indexes\Unique\UniqueBuilder;
use Umbrellio\Postgres\Schema\Builders\Indexes\Unique\UniquePartialBuilder;

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

    public function compileCreateView(Blueprint $blueprint, Fluent $command): string
    {
        $materialize = $command->get('materialize') ? 'materialized' : '';
        return implode(' ', array_filter([
            'create', $materialize, 'view',
            $this->wrapTable($command->get('view')),
            'as', $command->get('select'),
        ]));
    }

    public function compileCreateTrigger(Blueprint $blueprint, Fluent $command): void
    {
        dd($blueprint, $command);
    }

    public function compileCreateFunction(Blueprint $blueprint, Fluent $command): void
    {
        dd($blueprint, $command);
    }

    public function compileDropView(Blueprint $blueprint, Fluent $command): string
    {
        return 'drop view ' . $this->wrapTable($command->get('view'));
    }

    public function compileViewExists(): string
    {
        return 'select * from information_schema.views where table_schema = ? and table_name = ?';
    }

    public function compileViewDefinition(): string
    {
        return 'select view_definition from information_schema.views where table_schema = ? and table_name = ?';
    }

    public function compileUniquePartial(Blueprint $blueprint, UniqueBuilder $command): string
    {
        $constraints = $command->get('constraints');
        if ($constraints instanceof UniquePartialBuilder) {
            return UniqueCompiler::compile($this, $blueprint, $command, $constraints);
        }
        return $this->compileUnique($blueprint, $command);
    }

    public function compileExclude(Blueprint $blueprint, ExcludeBuilder $command): string
    {
        return ExcludeCompiler::compile($this, $blueprint, $command);
    }

    public function compileCheck(Blueprint $blueprint, CheckBuilder $command): string
    {
        return CheckCompiler::compile($this, $blueprint, $command);
    }

    protected function typeNumeric(Fluent $column): string
    {
        $type = 'numeric';
        $precision = $column->get('precision');
        $scale = $column->get('scale');

        if ($precision && $scale) {
            return "${type}({$precision}, {$scale})";
        }

        if ($precision) {
            return "${type}({$precision})";
        }

        return $type;
    }
}
