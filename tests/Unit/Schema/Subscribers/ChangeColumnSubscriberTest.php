<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Subscribers;

use Doctrine\DBAL\Event\SchemaAlterTableChangeColumnEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Collection;
use Umbrellio\Postgres\Schema\Subscribers\SchemaAlterTableChangeColumnSubscriber;
use Umbrellio\Postgres\Tests\TestCase;

/**
 * @property SchemaAlterTableChangeColumnSubscriber $subscriber
 * @property PostgreSqlPlatform $platform
 * @property TableDiff $tableDiff
 * @property ColumnDiff $columnDiff
 * @property SchemaAlterTableChangeColumnEventArgs $eventArgs
 * @property Column $column
 */
class ChangeColumnSubscriberTest extends TestCase
{
    private const TABLE = 'some_table';
    private const COLUMN = 'some_field';

    private $subscriber;
    private $platform;
    private $tableDiff;
    private $columnDiff;
    private $eventArgs;
    private $column;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriber = new SchemaAlterTableChangeColumnSubscriber();
        $this->platform = new PostgreSqlPlatform();

        $this->column = new Column(
            static::COLUMN,
            Type::getType('integer'),
            [
                // column options
            ]
        );

        $this->tableDiff = new TableDiff(
            static::TABLE,
            [],
            [
                // changed columns
            ],
            [],
            [],
            [],
            [],
            null
        );

        $this->columnDiff = new ColumnDiff(
            static::COLUMN,
            $this->column,
            [
                // changed properties
            ],
            $this->column
        );

        $this->eventArgs = new SchemaAlterTableChangeColumnEventArgs(
            $this->columnDiff,
            $this->tableDiff,
            $this->platform
        );
    }

    /** @test */
    public function getSubscriberEvents(): void
    {
        $this->assertSame([Events::onSchemaAlterTableChangeColumn], $this->subscriber->getSubscribedEvents());
    }

    /**
     * @test
     * @todo Допилить тесты
     */
    public function onSchemaAlterTableChangeColumn(): void
    {
        $this->subscriber->onSchemaAlterTableChangeColumn($this->eventArgs);
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @todo Допилить тесты
     */
    public function getAlterTableChangeColumnSQL(): void
    {
        $this->subscriber->onSchemaAlterTableChangeColumn($this->eventArgs);
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @todo Допилить тесты
     */
    public function compileAlterColumnComment(): void
    {
        $this->subscriber->compileAlterColumnComment(
            $this->platform,
            $this->columnDiff,
            $this->column,
            static::COLUMN,
            new Collection()
        );
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @todo Допилить тесты
     */
    public function compileAlterColumnNull(): void
    {
        $this->subscriber->compileAlterColumnNull(
            $this->columnDiff,
            $this->column,
            static::COLUMN,
            static::COLUMN,
            new Collection()
        );
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @todo Допилить тесты
     */
    public function compileAlterColumnDefault(): void
    {
        $this->subscriber->compileAlterColumnDefault(
            $this->platform,
            $this->columnDiff,
            $this->column,
            static::COLUMN,
            static::COLUMN,
            new Collection()
        );
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @todo Допилить тесты
     */
    public function compileAlterColumnSequence(): void
    {
        $this->subscriber->compileAlterColumnSequence(
            $this->platform,
            $this->columnDiff,
            $this->tableDiff,
            $this->column,
            static::COLUMN,
            static::COLUMN,
            new Collection()
        );
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @todo Допилить тесты
     */
    public function compileAlterColumnType(): void
    {
        $this->subscriber->compileAlterColumnType(
            $this->platform,
            $this->columnDiff,
            $this->column,
            static::COLUMN,
            static::COLUMN,
            new Collection()
        );
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @todo Допилить тесты
     */
    public function getDefaultValueDeclarationSQL(): void
    {
        $this->subscriber->getDefaultValueDeclarationSQL($this->platform, $this->column);
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @todo Допилить тесты
     */
    public function typeChangeBreaksDefaultValue(): void
    {
        $this->subscriber->typeChangeBreaksDefaultValue($this->columnDiff);
        $this->markTestIncomplete();
    }

    /** @test */
    public function isNumericType(): void
    {
        $this->assertTrue($this->subscriber->isNumericType(Type::getType('integer')));
        $this->assertFalse($this->subscriber->isNumericType(Type::getType('string')));
    }

    /**
     * @test
     * @todo Допилить тесты
     */
    public function quoteName(): void
    {
        $this->subscriber->quoteName($this->platform, $this->tableDiff);
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @todo Допилить тесты
     */
    public function getOldColumnComment(): void
    {
        $this->subscriber->getOldColumnComment($this->columnDiff);
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @todo Допилить тесты
     */
    public function getColumnComment(): void
    {
        $this->subscriber->getColumnComment($this->column);
        $this->markTestIncomplete();
    }
}
