<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers\Traits;

use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\Identifier;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\BigIntType;
use Doctrine\DBAL\Types\BinaryType;
use Doctrine\DBAL\Types\BlobType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\Type;
use Illuminate\Database\Query\Expression;

/**
 * @mixin PostgreSqlPlatform
 */
trait AlterTableSQLDeclarations
{
    public function getAlterTableAlterTypeColumnSQL(
        string $table,
        string $column,
        string $type,
        array $columnDefinition
    ): string {
        $using = sprintf('USING %s::%s', $column, $type);
        if ($columnDefinition['using'] ?? false) {
            $using = 'USING ' . $columnDefinition['using'];
        }
        return trim(sprintf('ALTER TABLE %s ALTER %s TYPE %s %s', $table, $column, $type, $using));
    }

    public function getAlterTableAlterDefaultSQL(string $table, string $columnName, Column $column): string
    {
        if ($column->getDefault() === null) {
            return sprintf('ALTER TABLE %s ALTER %s DROP DEFAULT', $table, $columnName);
        }

        return sprintf(
            'ALTER TABLE %s ALTER %s SET %s',
            $table,
            $columnName,
            trim($this->fixDefaultValueDeclarationSQL($column))
        );
    }

    public function fixDefaultValueDeclarationSQL(Column $column): string
    {
        if ($column->getDefault() instanceof Expression) {
            return ' DEFAULT ' . $column->getDefault();
        }
        return $this->getDefaultValueDeclarationSQL($column->toArray());
    }

    /**
     * {@inheritDoc}
     */
    public function getAlterTableSQL(TableDiff $diff)
    {
        $sql         = [];
        $commentsSQL = [];
        $columnSql   = [];

        foreach ($diff->addedColumns as $column) {
            if ($this->onSchemaAlterTableAddColumn($column, $diff, $columnSql)) {
                continue;
            }

            $query = 'ADD ' . $this->getColumnDeclarationSQL($column->getQuotedName($this), $column->toArray());
            $sql[] = 'ALTER TABLE ' . $diff->getName($this)->getQuotedName($this) . ' ' . $query;

            $comment = $this->getColumnComment($column);

            if ($comment === null || $comment === '') {
                continue;
            }

            $commentsSQL[] = $this->getCommentOnColumnSQL(
                $diff->getName($this)->getQuotedName($this),
                $column->getQuotedName($this),
                $comment
            );
        }

        foreach ($diff->removedColumns as $column) {
            if ($this->onSchemaAlterTableRemoveColumn($column, $diff, $columnSql)) {
                continue;
            }

            $query = 'DROP ' . $column->getQuotedName($this);
            $sql[] = 'ALTER TABLE ' . $diff->getName($this)->getQuotedName($this) . ' ' . $query;
        }

        foreach ($diff->changedColumns as $columnDiff) {
            /** @var $columnDiff \Doctrine\DBAL\Schema\ColumnDiff */
            if ($this->onSchemaAlterTableChangeColumn($columnDiff, $diff, $columnSql)) {
                continue;
            }

            if ($this->isUnchangedBinaryColumn($columnDiff)) {
                continue;
            }

            $oldColumnName = $columnDiff->getOldColumnName()->getQuotedName($this);
            $column        = $columnDiff->column;

            if ($columnDiff->hasChanged('type') || $columnDiff->hasChanged('precision') || $columnDiff->hasChanged('scale') || $columnDiff->hasChanged('fixed')) {
                $type = $column->getType();

                // SERIAL/BIGSERIAL are not "real" types and we can't alter a column to that type
                $columnDefinition                  = $column->toArray();
                $columnDefinition['autoincrement'] = false;

                // here was a server version check before, but DBAL API does not support this anymore.
                $sql[] = $this->getAlterTableAlterTypeColumnSQL(
                    $diff->getName($this)->getQuotedName($this),
                    $oldColumnName,
                    $type->getSQLDeclaration($columnDefinition, $this),
                    $columnDefinition
                );
            }

            if ($columnDiff->hasChanged('default') || $this->typeChangeBreaksDefaultValue($columnDiff)) {
                $sql[] = $this->getAlterTableAlterDefaultSQL(
                    $diff->getName($this)->getQuotedName($this),
                    $oldColumnName,
                    $column
                );
            }

            if ($columnDiff->hasChanged('notnull')) {
                $query = 'ALTER ' . $oldColumnName . ' ' . ($column->getNotnull() ? 'SET' : 'DROP') . ' NOT NULL';
                $sql[] = 'ALTER TABLE ' . $diff->getName($this)->getQuotedName($this) . ' ' . $query;
            }

            if ($columnDiff->hasChanged('autoincrement')) {
                if ($column->getAutoincrement()) {
                    // add autoincrement
                    $seqName = $this->getIdentitySequenceName($diff->name, $oldColumnName);

                    $sql[] = 'CREATE SEQUENCE ' . $seqName;
                    $sql[] = "SELECT setval('" . $seqName . "', (SELECT MAX(" . $oldColumnName . ') FROM ' . $diff->getName($this)->getQuotedName($this) . '))';
                    $query = 'ALTER ' . $oldColumnName . " SET DEFAULT nextval('" . $seqName . "')";
                    $sql[] = 'ALTER TABLE ' . $diff->getName($this)->getQuotedName($this) . ' ' . $query;
                } else {
                    // Drop autoincrement, but do NOT drop the sequence. It might be re-used by other tables or have
                    $query = 'ALTER ' . $oldColumnName . ' DROP DEFAULT';
                    $sql[] = 'ALTER TABLE ' . $diff->getName($this)->getQuotedName($this) . ' ' . $query;
                }
            }

            $newComment = $this->getColumnComment($column);
            $oldComment = $this->getOldColumnComment($columnDiff);

            if ($columnDiff->hasChanged('comment') || ($columnDiff->fromColumn !== null && $oldComment !== $newComment)) {
                $commentsSQL[] = $this->getCommentOnColumnSQL(
                    $diff->getName($this)->getQuotedName($this),
                    $column->getQuotedName($this),
                    $newComment
                );
            }

            if (! $columnDiff->hasChanged('length')) {
                continue;
            }

            $query = 'ALTER ' . $oldColumnName . ' TYPE ' . $column->getType()->getSQLDeclaration($column->toArray(), $this);
            $sql[] = 'ALTER TABLE ' . $diff->getName($this)->getQuotedName($this) . ' ' . $query;
        }

        foreach ($diff->renamedColumns as $oldColumnName => $column) {
            if ($this->onSchemaAlterTableRenameColumn($oldColumnName, $column, $diff, $columnSql)) {
                continue;
            }

            $oldColumnName = new Identifier($oldColumnName);

            $sql[] = 'ALTER TABLE ' . $diff->getName($this)->getQuotedName($this) .
                ' RENAME COLUMN ' . $oldColumnName->getQuotedName($this) . ' TO ' . $column->getQuotedName($this);
        }

        $tableSql = [];

        if (! $this->onSchemaAlterTable($diff, $tableSql)) {
            $sql = array_merge($sql, $commentsSQL);

            $newName = $diff->getNewName();

            if ($newName !== false) {
                $sql[] = sprintf(
                    'ALTER TABLE %s RENAME TO %s',
                    $diff->getName($this)->getQuotedName($this),
                    $newName->getQuotedName($this)
                );
            }

            $sql = array_merge(
                $this->getPreAlterTableIndexForeignKeySQL($diff),
                $sql,
                $this->getPostAlterTableIndexForeignKeySQL($diff)
            );
        }

        return array_merge($sql, $tableSql, $columnSql);
    }

    private function isUnchangedBinaryColumn(ColumnDiff $columnDiff): bool
    {
        $columnType = $columnDiff->column->getType();

        if (!$columnType instanceof BinaryType && !$columnType instanceof BlobType) {
            return false;
        }

        $fromColumn = $columnDiff->fromColumn instanceof Column ? $columnDiff->fromColumn : null;

        if ($fromColumn) {
            $fromColumnType = $fromColumn->getType();

            if (!$fromColumnType instanceof BinaryType && !$fromColumnType instanceof BlobType) {
                return false;
            }

            return count(array_diff($columnDiff->changedProperties, ['type', 'length', 'fixed'])) === 0;
        }

        if ($columnDiff->hasChanged('type')) {
            return false;
        }

        return count(array_diff($columnDiff->changedProperties, ['length', 'fixed'])) === 0;
    }

    private function typeChangeBreaksDefaultValue(ColumnDiff $columnDiff): bool
    {
        if (!$columnDiff->fromColumn) {
            return $columnDiff->hasChanged('type');
        }

        $oldTypeIsNumeric = $this->isNumericType($columnDiff->fromColumn->getType());
        $newTypeIsNumeric = $this->isNumericType($columnDiff->column->getType());

        // default should not be changed when switching between numeric types and the default comes from a sequence
        return $columnDiff->hasChanged('type')
            && !($oldTypeIsNumeric && $newTypeIsNumeric && $columnDiff->column->getAutoincrement());
    }

    private function isNumericType(Type $type): bool
    {
        return $type instanceof IntegerType || $type instanceof BigIntType;
    }

    private function getOldColumnComment(ColumnDiff $columnDiff): ?string
    {
        return $columnDiff->fromColumn ? $this->getColumnComment($columnDiff->fromColumn) : null;
    }
}
