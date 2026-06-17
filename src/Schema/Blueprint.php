<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema;

use Doctrine\DBAL\DriverManager;
use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Builders\Constraints\Check\CheckBuilder;
use Umbrellio\Postgres\Schema\Builders\Constraints\Exclude\ExcludeBuilder;
use Umbrellio\Postgres\Schema\Builders\Indexes\Unique\UniqueBuilder;
use Umbrellio\Postgres\Schema\Builders\Routines\CreateFunctionBuilder;
use Umbrellio\Postgres\Schema\Builders\Routines\CreateProcedureBuilder;
use Umbrellio\Postgres\Schema\Builders\Routines\CreateTriggerBuilder;
use Umbrellio\Postgres\Schema\Builders\Routines\DropFunctionBuilder;
use Umbrellio\Postgres\Schema\Builders\Routines\DropProcedureBuilder;
use Umbrellio\Postgres\Schema\Builders\Routines\DropTriggerBuilder;
use Umbrellio\Postgres\Schema\Definitions\Indexes\CheckDefinition;
use Umbrellio\Postgres\Schema\Definitions\Indexes\ExcludeDefinition;
use Umbrellio\Postgres\Schema\Definitions\Indexes\UniqueDefinition;
use Umbrellio\Postgres\Schema\Definitions\Routines\Functions\CreateFunctionDefinition;
use Umbrellio\Postgres\Schema\Definitions\Routines\Functions\DropFunctionDefinition;
use Umbrellio\Postgres\Schema\Definitions\Routines\Procedures\CreateProcedureDefinition;
use Umbrellio\Postgres\Schema\Definitions\Routines\Procedures\DropProcedureDefinition;
use Umbrellio\Postgres\Schema\Definitions\Routines\Triggers\CreateTriggerDefinition;
use Umbrellio\Postgres\Schema\Definitions\Routines\Triggers\DropTriggerDefinition;
use Umbrellio\Postgres\Schema\Definitions\Tables\AttachPartitionDefinition;
use Umbrellio\Postgres\Schema\Definitions\Tables\LikeDefinition;
use Umbrellio\Postgres\Schema\Definitions\Views\ViewDefinition;
use Umbrellio\Postgres\Schema\Types\DateRangeType;
use Umbrellio\Postgres\Schema\Types\TsRangeType;
use Umbrellio\Postgres\Schema\Types\TsTzRangeType;

class Blueprint extends BaseBlueprint
{
    protected $commands = [];

    /**
     * @return AttachPartitionDefinition|Fluent
     */
    public function attachPartition(string $partition): Fluent
    {
        return $this->addCommand('attachPartition', compact('partition'));
    }

    public function detachPartition(string $partition): void
    {
        $this->addCommand('detachPartition', compact('partition'));
    }

    /**
     * @codeCoverageIgnore
     * @return LikeDefinition|Fluent
     */
    public function like(string $table): Fluent
    {
        return $this->addCommand('like', compact('table'));
    }

    /**
     * @codeCoverageIgnore
     */
    public function ifNotExists(): Fluent
    {
        return $this->addCommand('ifNotExists');
    }

    /**
     * @param array|string $columns
     * @return UniqueDefinition|UniqueBuilder
     */
    public function uniquePartial($columns, ?string $index = null, ?string $algorithm = null): Fluent
    {
        $columns = (array) $columns;

        $index = $index ?: $this->createIndexName('unique', $columns);

        return $this->addExtendedCommand(
            UniqueBuilder::class,
            'uniquePartial',
            compact('columns', 'index', 'algorithm')
        );
    }

    public function dropUniquePartial($index): Fluent
    {
        return $this->dropIndexCommand('dropIndex', 'unique', $index);
    }

    /**
     * @param array|string $columns
     * @return ExcludeDefinition|ExcludeBuilder
     */
    public function exclude($columns, ?string $index = null): Fluent
    {
        $columns = (array) $columns;

        $index = $index ?: $this->createIndexName('excl', $columns);

        return $this->addExtendedCommand(ExcludeBuilder::class, 'exclude', compact('columns', 'index'));
    }

    /**
     * @param array|string $columns
     * @return CheckDefinition|CheckBuilder
     */
    public function check($columns, ?string $index = null): Fluent
    {
        $columns = (array) $columns;

        $index = $index ?: $this->createIndexName('chk', $columns);

        return $this->addExtendedCommand(CheckBuilder::class, 'check', compact('columns', 'index'));
    }

    public function dropExclude($index): Fluent
    {
        return $this->dropIndexCommand('dropUnique', 'excl', $index);
    }

    public function dropCheck($index): Fluent
    {
        return $this->dropIndexCommand('dropUnique', 'chk', $index);
    }

    /**
     * @codeCoverageIgnore
     */
    public function hasIndex($index, bool $unique = false): bool
    {
        if (is_array($index)) {
            $index = $this->createIndexName($unique === false ? 'index' : 'unique', $index);
        }

        return array_key_exists($index, $this->getSchemaManager()->listTableIndexes($this->getTable()));
    }

    /**
     * @codeCoverageIgnore
     * @return ViewDefinition|Fluent
     */
    public function createView(string $view, string $select, bool $materialize = false)
    {
        return $this->addCommand('createView', compact('view', 'select', 'materialize'));
    }

    /**
     * @return CreateTriggerDefinition
     */
    public function createTrigger(string $name)
    {
        return $this->addExtendedCommand(CreateTriggerBuilder::class, 'createTrigger', compact('name'));
    }

    /**
     * @return CreateFunctionDefinition
     */
    public function createFunction(string $name)
    {
        return $this->addExtendedCommand(CreateFunctionBuilder::class, 'createFunction', compact('name'));
    }

    /**
     * @return CreateProcedureDefinition
     */
    public function createProcedure(string $name)
    {
        return $this->addExtendedCommand(CreateProcedureBuilder::class, 'createProcedure', compact('name'));
    }

    /**
     * @return DropFunctionDefinition
     */
    public function dropFunction(string $name)
    {
        return $this->addExtendedCommand(DropFunctionBuilder::class, 'dropFunction', compact('name'));
    }

    /**
     * @return DropProcedureDefinition
     */
    public function dropProcedure(string $name)
    {
        return $this->addExtendedCommand(DropProcedureBuilder::class, 'dropProcedure', compact('name'));
    }

    /**
     * @codeCoverageIgnore
     */
    public function dropView(string $view): Fluent
    {
        return $this->addCommand('dropView', compact('view'));
    }

    /**
     * @return DropTriggerDefinition
     */
    public function dropTrigger(string $name, bool $dropDepends = false)
    {
        return $this->addExtendedCommand(DropTriggerBuilder::class, 'dropTrigger', compact('name', 'dropDepends'));
    }

    /**
     * Almost like 'decimal' type, but can be with variable precision (by default)
     *
     * @return Fluent|ColumnDefinition
     */
    public function numeric(string $column, ?int $precision = null, ?int $scale = null): Fluent
    {
        return $this->addColumn('numeric', $column, compact('precision', 'scale'));
    }

    /**
     * @return Fluent|ColumnDefinition
     */
    public function tsrange(string $column): Fluent
    {
        return $this->addColumn(TsRangeType::TYPE_NAME, $column);
    }

    /**
     * @return Fluent|ColumnDefinition
     */
    public function tstzrange(string $column): Fluent
    {
        return $this->addColumn(TsTzRangeType::TYPE_NAME, $column);
    }

    /**
     * @return Fluent|ColumnDefinition
     */
    public function daterange(string $column): Fluent
    {
        return $this->addColumn(DateRangeType::TYPE_NAME, $column);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function getSchemaManager()
    {
        /** @scrutinizer ignore-call */
        $connection = Schema::getConnection();
        $doctrineConnection = DriverManager::getConnection($connection->getConfig());
        return $doctrineConnection->getSchemaManager();
    }

    private function addExtendedCommand(string $fluent, string $name, array $parameters = []): Fluent
    {
        $command = new $fluent(array_merge(compact('name'), $parameters));
        $this->commands[] = $command;
        return $command;
    }
}
