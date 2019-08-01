<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Subscribers;

use Codeception\Util\ReflectionHelper;
use Doctrine\DBAL\Event\SchemaAlterTableChangeColumnEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use Illuminate\Database\Schema\Grammars\ChangeColumn;
use Illuminate\Support\Collection;
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
    private const COLUMN = 'some_field';

    private $subscriber;
    private $platform;
    private $tableDiff;
    private $columnDiff;
    private $eventArgs;
    private $columns;
    private $table;

    private $blueprint;
    private $postgresGrammar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->blueprint = new Blueprint(static::TABLE);
        $this->postgresGrammar = new PostgresGrammar();
        $this->subscriber = new SchemaAlterTableChangeColumnSubscriber();
        $this->platform = new PostgreSqlPlatform();
    }

    /** @test */
    public function getSubscriberEvents(): void
    {
        $this->assertSame([Events::onSchemaAlterTableChangeColumn], $this->subscriber->getSubscribedEvents());
    }

//    /** @test */
//    public function onSchemaAlterTableChangeColumn(): void
//    {
//
//
//        $this->subscriber->onSchemaAlterTableChangeColumn($eventArgs);
//        $this->assertTrue($eventArgs->isDefaultPrevented());
//    }

//    /**
//     * @test
//     * @todo Допилить тесты
//     */
//    public function getAlterTableChangeColumnSQL(): void
//    {
//        $this->subscriber->onSchemaAlterTableChangeColumn($this->eventArgs);
//        $this->markTestIncomplete();
//    }
//
//    /**
//     * @test
//     * @todo Допилить тесты
//     */
//    public function compileAlterColumnComment(): void
//    {
//        $this->subscriber->compileAlterColumnComment(
//            $this->platform,
//            $this->columnDiff,
//            $this->column,
//            static::COLUMN,
//            new Collection()
//        );
//        $this->markTestIncomplete();
//    }
//
//    /**
//     * @test
//     * @todo Допилить тесты
//     */
//    public function compileAlterColumnNull(): void
//    {
//        $this->subscriber->compileAlterColumnNull(
//            $this->columnDiff,
//            $this->column,
//            static::COLUMN,
//            static::COLUMN,
//            new Collection()
//        );
//        $this->markTestIncomplete();
//    }
//
//    /**
//     * @test
//     * @todo Допилить тесты
//     */
//    public function compileAlterColumnDefault(): void
//    {
//        $this->subscriber->compileAlterColumnDefault(
//            $this->platform,
//            $this->columnDiff,
//            $this->column,
//            static::COLUMN,
//            static::COLUMN,
//            new Collection()
//        );
//        $this->markTestIncomplete();
//    }

    /** @test */
    public function dropSequence(): void
    {
        $this->blueprint->integer('some_key')->change();
        $eventArgs = $this->getEventArgsForColumn('some_key');

        $this->subscriber->onSchemaAlterTableChangeColumn($eventArgs);

        $this->assertSame(
            [
                'ALTER TABLE some_table ALTER some_key DROP DEFAULT',
                'ALTER TABLE some_table ALTER some_key TYPE INT USING some_key::INT',
            ],
            $eventArgs->getSql()
        );
    }

    /** @test */
    public function createSequence(): void
    {
        $this->blueprint->integer('some_key')->change();
        $this->blueprint->increments('some_integer_default')->change();

        $eventArgs = $this->getEventArgsForColumn('some_integer_default');
        $this->subscriber->onSchemaAlterTableChangeColumn($eventArgs);

        $this->assertSame(
            [
                'CREATE SEQUENCE some_table_some_integer_default_seq',
                "SELECT setval('some_table_some_integer_default_seq', (SELECT MAX(some_integer_default) FROM some_table))",
                "ALTER TABLE some_table ALTER some_integer_default SET DEFAULT nextval('some_table_some_integer_default_seq')",
            ],
            $eventArgs->getSql()
        );
    }

//    /**
//     * @test
//     * @todo Допилить тесты
//     */
//    public function compileAlterColumnType(): void
//    {
//        $this->subscriber->compileAlterColumnType(
//            $this->platform,
//            $this->columnDiff,
//            $this->column,
//            static::COLUMN,
//            static::COLUMN,
//            new Collection()
//        );
//        $this->markTestIncomplete();
//    }

//    /**
//     * @test
//     * @todo Допилить тесты
//     */
//    public function getDefaultValueDeclarationSQL(): void
//    {
//        $this->subscriber->getDefaultValueDeclarationSQL($this->platform, $this->column);
//        $this->markTestIncomplete();
//    }
//
//    /**
//     * @test
//     * @todo Допилить тесты
//     */
//    public function typeChangeBreaksDefaultValue(): void
//    {
//        $this->subscriber->typeChangeBreaksDefaultValue($this->columnDiff);
//        $this->markTestIncomplete();
//    }
//
//    /** @test */
//    public function isNumericType(): void
//    {
//        $this->assertTrue($this->subscriber->isNumericType(Type::getType('integer')));
//        $this->assertFalse($this->subscriber->isNumericType(Type::getType('string')));
//    }
//
//    /**
//     * @test
//     * @todo Допилить тесты
//     */
//    public function quoteName(): void
//    {
//        $this->subscriber->quoteName($this->platform, $this->tableDiff);
//        $this->markTestIncomplete();
//    }
//
//    /**
//     * @test
//     * @todo Допилить тесты
//     */
//    public function getOldColumnComment(): void
//    {
//        $this->subscriber->getOldColumnComment($this->columnDiff);
//        $this->markTestIncomplete();
//    }
//
//    /**
//     * @test
//     * @todo Допилить тесты
//     */
//    public function getColumnComment(): void
//    {
//        $this->subscriber->getColumnComment($this->column);
//        $this->markTestIncomplete();
//    }

    private function getEventArgsForColumn(string $columnName): SchemaAlterTableChangeColumnEventArgs
    {
        /** @var PostgresConnection $connection */
        $connection = DB::connection();
        $schemaManager = $connection->getDoctrineSchemaManager();

        $this->columns = [];
        foreach ($this->getListColumns() as $listColumn) {
            $this->columns[] = ReflectionHelper::invokePrivateMethod(
                $schemaManager,
                '_getPortableTableColumnDefinition',
                ['tableName' => $listColumn]
            );
        }

        $this->table = new Table(static::TABLE, $this->columns);

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

    private function getListColumns(): array
    {
        return [
            //            $this->getDefinitionLastValue(),
            $this->getDefinitionSomeKeySequence(),
            //            $this->getDefinitionSomeKey(),
            $this->getDefinitionSomeString(),
            //            $this->getDefinitionLogCnt(),
            $this->getDefinitionSomeStringDefault(),
            //            $this->getDefinitionIsCalled(),
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

    private function getDefinitionLastValue(): array
    {
        return [
            'attnum' => 1,
            'field' => 'last_value',
            'type' => 'int8',
            'complete_type' => 'bigint',
            'domain_type' => null,
            'domain_complete_type' => null,
            'isnotnull' => true,
            'pri' => null,
            'default' => null,
            'comment' => null,
        ];
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

    private function getDefinitionSomeKey(): array
    {
        return [
            'attnum' => 1,
            'field' => 'some_key',
            'type' => 'int8',
            'complete_type' => 'bigint',
            'domain_type' => null,
            'domain_complete_type' => null,
            'isnotnull' => false,
            'pri' => null,
            'default' => null,
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

    private function getDefinitionLogCnt(): array
    {
        return [
            'attnum' => 2,
            'field' => 'log_cnt',
            'type' => 'int8',
            'complete_type' => 'bigint',
            'domain_type' => null,
            'domain_complete_type' => null,
            'isnotnull' => true,
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

    private function getDefinitionIsCalled(): array
    {
        return [
            'attnum' => 2,
            'field' => 'is_called',
            'type' => 'bool',
            'complete_type' => 'boolean',
            'domain_type' => null,
            'domain_complete_type' => null,
            'isnotnull' => true,
            'pri' => null,
            'default' => null,
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
