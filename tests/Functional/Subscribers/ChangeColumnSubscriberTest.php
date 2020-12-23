<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Subscribers;

use Closure;
use Codeception\Util\ReflectionHelper;
use Doctrine\DBAL\Event\SchemaAlterTableChangeColumnEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Platforms\PostgreSQL94Platform as PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Generator;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Grammars\ChangeColumn;
use Illuminate\Support\Facades\DB;
use ReflectionMethod;
use Umbrellio\Postgres\PostgresConnection;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;
use Umbrellio\Postgres\Schema\Subscribers\SchemaAlterTableChangeColumnSubscriber;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

/**
 * @property SchemaAlterTableChangeColumnSubscriber $subscriber
 * @property PostgreSqlPlatform $platform
 * @property TableDiff $tableDiff
 * @property ColumnDiff $columnDiff
 * @property SchemaAlterTableChangeColumnEventArgs $eventArgs
 * @property Column[] $columns
 * @property Table $table
 * @property Blueprint $blueprint
 * @property PostgresGrammar $postgresGrammar
 */
class ChangeColumnSubscriberTest extends FunctionalTestCase
{
    private const TABLE = 'some_table';

    private $subscriber;
    private $platform;
    private $tableDiff;
    private $columnDiff;
    private $columns;
    private $table;

    private $blueprint;
    private $postgresGrammar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->blueprint = new Blueprint('some_table');
        $this->postgresGrammar = new PostgresGrammar();
        $this->subscriber = new SchemaAlterTableChangeColumnSubscriber();
        $this->platform = new PostgreSqlPlatform();
    }

    /**
     * @test
     */
    public function getSubscriberEvents(): void
    {
        $this->assertSame([Events::onSchemaAlterTableChangeColumn], $this->subscriber->getSubscribedEvents());
    }

    /**
     * @test
     * @group forPHP7
     * @dataProvider provideSchemas
     */
    public function changeSchema7(string $column, Closure $callback, array $expectedSQL): void
    {
        $callback($this->blueprint, $column);
        $eventArgs = $this->getEventArgsForColumn($column);
        $this->subscriber->onSchemaAlterTableChangeColumn($eventArgs);

        $this->assertSame($expectedSQL, $eventArgs->getSql());
    }

    /**
     * @test
     * @group forPHP8
     * @dataProvider provideSchemas
     */
    public function changeSchema8(string $column, Closure $callback, array $expectedSQL): void
    {
        $callback($this->blueprint, $column);
        $eventArgs = $this->getEventArgsForColumn($column, 'tableColumn');
        $this->subscriber->onSchemaAlterTableChangeColumn($eventArgs);

        $this->assertSame($expectedSQL, $eventArgs->getSql());
    }

    public function provideSchemas(): Generator
    {
        yield $this->dropCommentCase();
        yield $this->changeCommentCase();
        yield $this->dropNotNullCase();
        yield $this->createSequenceCase();
        yield $this->dropDefaultCase();
        yield $this->setSimpleDefaultCase();
        yield $this->setExpressionDefaultCase();
        yield $this->changeTypeWithUsingCase();
        yield $this->changeLengthCase();
    }

    private function getEventArgsForColumn(
        string $columnName,
        string $argumentName = 'tableName'
    ): SchemaAlterTableChangeColumnEventArgs {
        /** @var PostgresConnection $connection */
        $connection = DB::connection();
        $schemaManager = $connection->getDoctrineSchemaManager();

        $this->columns = [];
        foreach ($this->getListColumns() as $listColumn) {
            $this->columns[] = ReflectionHelper::invokePrivateMethod(
                $schemaManager,
                '_getPortableTableColumnDefinition',
                [
                    $argumentName => $listColumn,
                ]
            );
        }

        $this->table = new Table('some_table', $this->columns);

        $this->tableDiff = (new Comparator())->diffTable(
            $this->table,
            $this
                ->getStaticMethod(ChangeColumn::class, 'getTableWithColumnChanges')
                ->invoke(null, $this->blueprint, $this->table)
        );

        foreach ($this->tableDiff->changedColumns as $columnDiff) {
            if ($columnDiff->oldColumnName !== $columnName) {
                continue;
            }
            $this->columnDiff = $columnDiff;
        }

        return new SchemaAlterTableChangeColumnEventArgs($this->columnDiff, $this->tableDiff, $this->platform);
    }

    private function dropCommentCase(): array
    {
        return [
            'some_comment',
            function (Blueprint $table, string $column) {
                $table->string($column)
                    ->nullable(false)
                    ->change();
            },
            ['ALTER TABLE some_table ALTER some_comment SET NOT NULL'],
        ];
    }

    private function changeCommentCase(): array
    {
        return [
            'some_comment',
            function (Blueprint $table, string $column) {
                $table->string($column)
                    ->comment('new_comment')
                    ->change();
            },
            ["COMMENT ON COLUMN some_table.some_comment IS 'new_comment'"],
        ];
    }

    private function dropNotNullCase(): array
    {
        return [
            'some_integer_default',
            function (Blueprint $table, string $column) {
                $table->integer($column)
                    ->nullable()
                    ->change();
            },
            ['ALTER TABLE some_table ALTER some_integer_default DROP NOT NULL'],
        ];
    }

    private function createSequenceCase(): array
    {
        return [
            'some_integer_default',
            function (Blueprint $table, string $column) {
                $table->increments($column)
                    ->change();
            },
            [
                'CREATE SEQUENCE some_table_some_integer_default_seq',
                "SELECT setval('some_table_some_integer_default_seq', (SELECT MAX(some_integer_default) FROM some_table))",
                "ALTER TABLE some_table ALTER some_integer_default SET DEFAULT nextval('some_table_some_integer_default_seq')",
            ],
        ];
    }

    private function dropDefaultCase(): array
    {
        return [
            'some_key',
            function (Blueprint $table, string $column) {
                $table->integer($column)
                    ->change();
            },
            [
                'ALTER TABLE some_table ALTER some_key DROP DEFAULT',
                'ALTER TABLE some_table ALTER some_key TYPE INT USING some_key::INT',
            ],
        ];
    }

    private function setSimpleDefaultCase(): array
    {
        return [
            'some_comment',
            function (Blueprint $table, string $column) {
                $table->string($column)
                    ->default('some_default')
                    ->change();
            },
            ["ALTER TABLE some_table ALTER some_comment SET DEFAULT 'some_default'"],
        ];
    }

    private function setExpressionDefaultCase(): array
    {
        return [
            'some_comment',
            function (Blueprint $table, string $column) {
                $table->string($column)
                    ->default(new Expression("('some_string:' || some_comment)::character varying"))
                    ->change();
            },
            [
                "ALTER TABLE some_table ALTER some_comment SET DEFAULT ('some_string:' || some_comment)::character varying",
            ],
        ];
    }

    private function changeTypeWithUsingCase(): array
    {
        return [
            'some_integer_default',
            function (Blueprint $table, string $column) {
                $table
                    ->text($column)
                    ->default(null)
                    ->using(sprintf("('[some_exp:' || %s || ']')::character varying", $column))
                    ->change();
            },
            [
                'ALTER TABLE some_table ALTER some_integer_default DROP DEFAULT',
                "ALTER TABLE some_table ALTER some_integer_default TYPE TEXT USING ('[some_exp:' || some_integer_default || ']')::character varying",
            ],
        ];
    }

    private function changeLengthCase(): array
    {
        return [
            'some_comment',
            function (Blueprint $table, string $column) {
                $table->string($column, 75)
                    ->change();
            },
            ['ALTER TABLE some_table ALTER some_comment TYPE VARCHAR(75)'],
        ];
    }

    private function getListColumns(): array
    {
        return [
            $this->getDefinitionSomeKeySequence(),
            $this->getDefinitionSomeString(),
            $this->getDefinitionSomeStringDefault(),
            $this->getDefinitionSomeIntegerDefault(),
            $this->getDefinitionSomeComment(),
        ];
    }

    private function getStaticMethod($class, $method): ReflectionMethod
    {
        $method = new ReflectionMethod($class, $method);
        $method->setAccessible(true);

        return $method;
    }

    private function getDefinitionSomeKeySequence(): array
    {
        return [
            'attnum' => 1,
            'field' => 'some_key',
            'type' => 'int8',
            'complete_type' => 'bigint',
            'domain_type' => null,
            'domain_complete_type' => null,
            'isnotnull' => true,
            'pri' => 't',
            'default' => "nextval('some_table_some_key_seq'::regclass)",
            'comment' => null,
        ];
    }

    private function getDefinitionSomeString(): array
    {
        return [
            'attnum' => 2,
            'field' => 'some_string',
            'type' => 'varchar',
            'complete_type' => 'character varying',
            'domain_type' => null,
            'domain_complete_type' => null,
            'isnotnull' => false,
            'pri' => null,
            'default' => null,
            'comment' => null,
        ];
    }

    private function getDefinitionSomeStringDefault(): array
    {
        return [
            'attnum' => 3,
            'field' => 'some_string_default',
            'type' => 'varchar',
            'complete_type' => 'character varying',
            'domain_type' => null,
            'domain_complete_type' => null,
            'isnotnull' => true,
            'pri' => null,
            'default' => "'some_default_value'::character varying",
            'comment' => null,
        ];
    }

    private function getDefinitionSomeIntegerDefault(): array
    {
        return [
            'attnum' => 4,
            'field' => 'some_integer_default',
            'type' => 'int4',
            'complete_type' => 'integer',
            'domain_type' => null,
            'domain_complete_type' => null,
            'isnotnull' => true,
            'pri' => null,
            'default' => 1,
            'comment' => null,
        ];
    }

    private function getDefinitionSomeComment(): array
    {
        return [
            'attnum' => 5,
            'field' => 'some_comment',
            'type' => 'varchar',
            'complete_type' => 'character varying',
            'domain_type' => null,
            'domain_complete_type' => null,
            'isnotnull' => false,
            'pri' => null,
            'default' => null,
            'comment' => 'some_comment_value',
        ];
    }
}
