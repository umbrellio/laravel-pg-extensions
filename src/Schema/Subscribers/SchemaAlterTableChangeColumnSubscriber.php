<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Subscribers;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\SchemaAlterTableChangeColumnEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\IntegerType;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;

final class SchemaAlterTableChangeColumnSubscriber implements EventSubscriber
{
    public function onSchemaAlterTableChangeColumn(SchemaAlterTableChangeColumnEventArgs $event): void
    {
        $event->preventDefault();

        $sql = $this->getAlterTableChangeColumnSQL(
            $event->getPlatform(),
            $event->getTableDiff(),
            $event->getColumnDiff()
        );

        $event->addSql($sql->unique()->toArray());
    }

    public function getSubscribedEvents(): array
    {
        return [Events::onSchemaAlterTableChangeColumn];
    }

    public function getAlterTableChangeColumnSQL(
        AbstractPlatform $platform,
        TableDiff $diff,
        ColumnDiff $columnDiff
    ): Collection {
        $sql = new Collection();

        $quoteName = $this->quoteName($platform, $diff);

        $oldColumnName = $columnDiff
            ->getOldColumnName()
            ->getQuotedName($platform);
        $column = $columnDiff->column;

        $this->compileAlterColumnType($platform, $columnDiff, $column, $quoteName, $oldColumnName, $sql);

        $this->compileAlterColumnDefault($platform, $columnDiff, $column, $quoteName, $oldColumnName, $sql);

        $this->compileAlterColumnNull($columnDiff, $column, $quoteName, $oldColumnName, $sql);

        $this->compileAlterColumnSequence($platform, $columnDiff, $diff, $column, $quoteName, $oldColumnName, $sql);

        $this->compileAlterColumnComment($platform, $columnDiff, $column, $quoteName, $sql);

        if (!$columnDiff->hasChanged('length')) {
            return $sql;
        }

        $sql->add(sprintf(
            'ALTER TABLE %s ALTER %s TYPE %s',
            $quoteName,
            $oldColumnName,
            $column
                ->getType()
                ->getSQLDeclaration($column->toArray(), $platform)
        ));

        return $sql;
    }

    public function compileAlterColumnComment(
        AbstractPlatform $platform,
        ColumnDiff $columnDiff,
        Column $column,
        string $quoteName,
        Collection $sql
    ): void {
        $newComment = $this->getColumnComment($column);
        $oldComment = $this->getOldColumnComment($columnDiff);

        if (($columnDiff->fromColumn !== null && $oldComment !== $newComment)
            || $columnDiff->hasChanged('comment')
        ) {
            $sql->add($platform->getCommentOnColumnSQL($quoteName, $column->getQuotedName($platform), $newComment));
        }
    }

    public function compileAlterColumnNull(
        ColumnDiff $columnDiff,
        Column $column,
        string $quoteName,
        string $oldColumnName,
        Collection $sql
    ): void {
        if ($columnDiff->hasChanged('notnull')) {
            $sql->add(sprintf(
                'ALTER TABLE %s ALTER %s %s NOT NULL',
                $quoteName,
                $oldColumnName,
                ($column->getNotnull() ? 'SET' : 'DROP')
            ));
        }
    }

    public function compileAlterColumnDefault(
        AbstractPlatform $platform,
        ColumnDiff $columnDiff,
        Column $column,
        string $quoteName,
        string $oldColumnName,
        Collection $sql
    ): void {
        if ($columnDiff->hasChanged('default') || $this->typeChangeBreaksDefaultValue($columnDiff)) {
            $defaultClause = $column->getDefault() === null
                ? ' DROP DEFAULT'
                : ' SET' . $this->getDefaultValueDeclarationSQL($platform, $column);
            $sql->add(sprintf('ALTER TABLE %s ALTER %s %s', $quoteName, $oldColumnName, trim($defaultClause)));
        }
    }

    public function compileAlterColumnSequence(
        AbstractPlatform $platform,
        ColumnDiff $columnDiff,
        TableDiff $diff,
        Column $column,
        string $quoteName,
        string $oldColumnName,
        Collection $sql
    ): void {
        if (!$columnDiff->hasChanged('autoincrement')) {
            return;
        }

        if (!$column->getAutoincrement()) {
            $sql->add(sprintf('ALTER TABLE %s ALTER %s DROP DEFAULT', $quoteName, $oldColumnName));
            return;
        }

        $seqName = $platform->getIdentitySequenceName($diff->name, $oldColumnName);

        $sql->add(sprintf('CREATE SEQUENCE %s', $seqName));
        $sql->add(sprintf("SELECT setval('%s', (SELECT MAX(%s) FROM %s))", $seqName, $oldColumnName, $quoteName));
        $sql->add(sprintf("ALTER TABLE %s ALTER %s SET DEFAULT nextval('%s')", $quoteName, $oldColumnName, $seqName));
    }

    public function compileAlterColumnType(
        AbstractPlatform $platform,
        ColumnDiff $columnDiff,
        Column $column,
        string $quoteName,
        string $oldColumnName,
        Collection $sql
    ): void {
        if (!$columnDiff->hasChanged('type')
            && !$columnDiff->hasChanged('precision')
            && !$columnDiff->hasChanged('scale')
            && !$columnDiff->hasChanged('fixed')
        ) {
            return;
        }

        $type = $column->getType();

        $columnDefinition = $column->toArray();
        $columnDefinition['autoincrement'] = false;

        if ($this->typeChangeBreaksDefaultValue($columnDiff)) {
            $sql->add(sprintf('ALTER TABLE %s ALTER %s DROP DEFAULT', $quoteName, $oldColumnName));
        }

        $typeName = $type->getSQLDeclaration($columnDefinition, $platform);

        if ($columnDiff->hasChanged('type')) {
            $using = sprintf('USING %s::%s', $oldColumnName, $typeName);

            if ($columnDefinition['using'] ?? false) {
                $using = 'USING ' . $columnDefinition['using'];
            }
        }

        $sql->add(trim(sprintf(
            'ALTER TABLE %s ALTER %s TYPE %s %s',
            $quoteName,
            $oldColumnName,
            $typeName,
            $using ?? ''
        )));
    }

    public function getDefaultValueDeclarationSQL(AbstractPlatform $platform, Column $column): string
    {
        if ($column->getDefault() instanceof Expression) {
            return ' DEFAULT ' . $column->getDefault();
        }

        return $platform->getDefaultValueDeclarationSQL($column->toArray());
    }

    public function typeChangeBreaksDefaultValue(ColumnDiff $columnDiff): bool
    {
        $oldTypeIsNumeric = $this->isNumericType($columnDiff->fromColumn);
        $newTypeIsNumeric = $this->isNumericType($columnDiff->column);

        $isNumeric = !($oldTypeIsNumeric && $newTypeIsNumeric && $columnDiff->column->getAutoincrement());

        return $columnDiff->hasChanged('type') && $isNumeric;
    }

    public function isNumericType(?Column $column): bool
    {
        $type = $column ? $column->getType() : null;

        return $type instanceof IntegerType || $type instanceof BigIntType;
    }

    public function quoteName(AbstractPlatform $platform, TableDiff $diff): string
    {
        return $diff
            ->getName($platform)
            ->getQuotedName($platform);
    }

    public function getOldColumnComment(ColumnDiff $columnDiff): ?string
    {
        return $columnDiff->fromColumn ? $this->getColumnComment($columnDiff->fromColumn) : null;
    }

    public function getColumnComment(Column $column): ?string
    {
        return $column->getComment();
    }
}
