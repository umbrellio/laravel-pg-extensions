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
use Umbrellio\Postgres\Schema\Types\DateRangeType;
use Umbrellio\Postgres\Schema\Types\NumericType;
use Umbrellio\Postgres\Schema\Types\TsRangeType;

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
        return sprintf(
            'alter table %s detach partition %s',
            $this->wrapTable($blueprint),
            $command->get('partition')
        );
    }

    public function compileCreateView(/** @scrutinizer ignore-unused */ Blueprint $blueprint, Fluent $command): string
    {
        $materialize = $command->get('materialize') ? 'materialized' : '';
        return implode(' ', array_filter([
            'create',
            $materialize,
            'view',
            $this->wrapTable($command->get('view')),
            'as',
            $command->get('select'),
        ]));
    }

    public function compileDropView(/** @scrutinizer ignore-unused */ Blueprint $blueprint, Fluent $command): string
    {
        return 'drop view ' . $this->wrapTable($command->get('view'));
    }

    public function compileViewExists(): string
    {
        return 'select * from information_schema.views where table_schema = ? and table_name = ?';
    }

    public function compileForeignKeysListing(string $tableName): string
    {
        return sprintf("
            SELECT
                kcu.column_name as source_column_name,
                ccu.table_name AS target_table_name,
                ccu.column_name AS target_column_name
            FROM
                information_schema.table_constraints AS tc
                    JOIN information_schema.key_column_usage AS kcu
                         ON tc.constraint_name = kcu.constraint_name
                             AND tc.table_schema = kcu.table_schema
                    JOIN information_schema.constraint_column_usage AS ccu
                         ON ccu.constraint_name = tc.constraint_name
                             AND ccu.table_schema = tc.table_schema
            WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_name='%s';
        ", $tableName);
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
        $type = NumericType::TYPE_NAME;
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

    protected function typeTsrange(/** @scrutinizer ignore-unused */ Fluent $column): string
    {
        return TsRangeType::TYPE_NAME;
    }

    protected function typeTstzrange(/** @scrutinizer ignore-unused */ Fluent $column): string
    {
        return TsTxRangeType::TYPE_NAME;
    }

    protected function typeDaterange(/** @scrutinizer ignore-unused */ Fluent $column): string
    {
        return DateRangeType::TYPE_NAME;
    }
}
