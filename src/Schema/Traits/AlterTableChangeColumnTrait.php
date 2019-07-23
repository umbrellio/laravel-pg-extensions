<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Traits;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\Type;
use Illuminate\Database\Query\Expression;

trait AlterTableChangeColumnTrait
{
    public function getAlterTableChangeColumnSQL(
        AbstractPlatform $platform,
        TableDiff $diff,
        ColumnDiff $columnDiff
    ): array {
        $sql = [];

        $quoteName = $this->quoteName($platform, $diff);

        $oldColumnName = $columnDiff->getOldColumnName()->getQuotedName($platform);
        $column = $columnDiff->column;

        $this->compileAlterColumnType($platform, $columnDiff, $column, $quoteName, $oldColumnName, $sql);

        $this->compileAlterColumnDefault($platform, $columnDiff, $column, $quoteName, $oldColumnName, $sql);

        $this->compileAlterColumnNull($columnDiff, $column, $quoteName, $oldColumnName, $sql);

        $this->compileAlterColumnSequence($platform, $columnDiff, $diff, $column, $quoteName, $oldColumnName, $sql);

        $this->compileAlterColumnComment($platform, $columnDiff, $column, $quoteName, $sql);

        if (!$columnDiff->hasChanged('length')) {
            return $sql;
        }

        $sql[] = sprintf(
            'ALTER TABLE %s ALTER %s TYPE %s',
            $quoteName,
            $oldColumnName,
            $column->getType()->getSQLDeclaration($column->toArray(), $platform)
        );

        return $sql;
    }

    private function compileAlterColumnComment(
        AbstractPlatform $platform,
        ColumnDiff $columnDiff,
        Column $column,
        string $quoteName,
        &$sql
    ): void {
        $newComment = $this->getColumnComment($column);
        $oldComment = $this->getOldColumnComment($columnDiff);

        if (($columnDiff->fromColumn !== null && $oldComment !== $newComment)
            || $columnDiff->hasChanged('comment')
        ) {
            $sql[] = $platform->getCommentOnColumnSQL($quoteName, $column->getQuotedName($platform), $newComment);
        }
    }

    private function compileAlterColumnNull(
        ColumnDiff $columnDiff,
        Column $column,
        string $quoteName,
        string $oldColumnName,
        &$sql
    ): void {
        if ($columnDiff->hasChanged('notnull')) {
            $sql[] = sprintf(
                'ALTER TABLE %s ALTER %s %s NOT NULL',
                $quoteName,
                $oldColumnName,
                ($column->getNotnull() ? 'SET' : 'DROP')
            );
        }
    }

    private function compileAlterColumnDefault(
        AbstractPlatform $platform,
        ColumnDiff $columnDiff,
        Column $column,
        string $quoteName,
        string $oldColumnName,
        &$sql
    ): void {
        if ($columnDiff->hasChanged('default') || $this->typeChangeBreaksDefaultValue($columnDiff)) {
            $defaultClause = $column->getDefault() === null
                ? ' DROP DEFAULT'
                : ' SET' . $this->getDefaultValueDeclarationSQL($platform, $column);
            $sql[] = sprintf('ALTER TABLE %s ALTER %s %s', $quoteName, $oldColumnName, trim($defaultClause));
        }
    }

    private function compileAlterColumnSequence(
        AbstractPlatform $platform,
        ColumnDiff $columnDiff,
        TableDiff $diff,
        Column $column,
        string $quoteName,
        string $oldColumnName,
        &$sql
    ): void {
        if (!$columnDiff->hasChanged('autoincrement')) {
            return;
        }

        if (!$column->getAutoincrement()) {
            $sql[] = sprintf('ALTER TABLE %s ALTER %s DROP DEFAULT', $quoteName, $oldColumnName);
            return;
        }

        $seqName = $platform->getIdentitySequenceName($diff->name, $oldColumnName);

        $sql[] = sprintf('CREATE SEQUENCE %s', $seqName);
        $sql[] = sprintf("SELECT setval('%s', (SELECT MAX(%s) FROM %s))", $seqName, $oldColumnName, $quoteName);
        $sql[] = sprintf("ALTER TABLE %s ALTER %s SET DEFAULT nextval('%s')", $quoteName, $oldColumnName, $seqName);
    }

    private function compileAlterColumnType(
        AbstractPlatform $platform,
        ColumnDiff $columnDiff,
        Column $column,
        string $quoteName,
        string $oldColumnName,
        &$sql
    ): void {
        if ($columnDiff->hasChanged('type')
            || $columnDiff->hasChanged('precision')
            || $columnDiff->hasChanged('scale')
            || $columnDiff->hasChanged('fixed')
        ) {
            $type = $column->getType();

            $columnDefinition = $column->toArray();
            $columnDefinition['autoincrement'] = false;

            if ($this->typeChangeBreaksDefaultValue($columnDiff)) {
                $sql[] = sprintf('ALTER TABLE %s ALTER %s DROP DEFAULT', $quoteName, $oldColumnName);
            }

            $typeName = $type->getSQLDeclaration($columnDefinition, $platform);

            if ($columnDiff->hasChanged('type')) {
                $using = sprintf('USING %s::%s', $oldColumnName, $typeName);

                if ($columnDefinition['using'] ?? false) {
                    $using = 'USING ' . $columnDefinition['using'];
                }
            }

            $sql[] = trim(sprintf(
                'ALTER TABLE %s ALTER %s TYPE %s %s',
                $quoteName,
                $oldColumnName,
                $typeName,
                $using ?? ''
            ));
        }
    }

    private function getDefaultValueDeclarationSQL(AbstractPlatform $platform, Column $column): string
    {
        if ($column->getDefault() instanceof Expression) {
            return ' DEFAULT ' . $column->getDefault();
        }

        return $platform->getDefaultValueDeclarationSQL($column->toArray());
    }

    private function typeChangeBreaksDefaultValue(ColumnDiff $columnDiff): bool
    {
        $oldTypeIsNumeric = $this->isNumericType($columnDiff->fromColumn->getType());
        $newTypeIsNumeric = $this->isNumericType($columnDiff->column->getType());

        return $columnDiff->hasChanged('type')
            && !($oldTypeIsNumeric && $newTypeIsNumeric && $columnDiff->column->getAutoincrement());
    }

    private function isNumericType(Type $type): bool
    {
        return $type instanceof IntegerType || $type instanceof BigIntType;
    }

    private function quoteName(AbstractPlatform $platform, TableDiff $diff): string
    {
        return $diff->getName($platform)->getQuotedName($platform);
    }

    private function getOldColumnComment(ColumnDiff $columnDiff): ?string
    {
        return $columnDiff->fromColumn ? $this->getColumnComment($columnDiff->fromColumn) : null;
    }

    private function getColumnComment(Column $column): ?string
    {
        return $column->getComment();
    }
}
