<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Grammars;

use Illuminate\Database\Schema\Grammars\PostgresGrammar as BasePostgresGrammar;
use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Compilers\AttachPartitionCompiler;
use Umbrellio\Postgres\Compilers\CreateCompiler;
use Umbrellio\Postgres\Schema\Blueprint;

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

    public function compileCreateView(Blueprint $blueprint, Fluent $command): string
    {
        $materialize = $command->get('materialize') ? 'materialized' : '';
        return implode(' ', array_filter([
            'create', $materialize, 'view',
            $this->wrapTable($command->get('view')),
            'as', $command->get('select'),
        ]));
    }

    public function compileDropView(Blueprint $blueprint, Fluent $command): string
    {
        return 'drop view ' . $this->wrapTable($command->get('view'));
    }

    public function compileViewExists(): string
    {
        return 'select * from information_schema.views where table_schema = ? and table_name = ?';
    }

    public function compileColumnListing(): string
    {
        return 'select view_definition from information_schema.views where table_schema = ? and table_name = ?';
    }
}
