<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers\Traits;

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
 * @method bool onSchemaAlterTableAddColumn(Column $column, TableDiff $diff, &$columnSql)
 * @method string getColumnDeclarationSQL($name, array $field)
 * @method string|null getColumnComment(Column $column)
 * @method getCommentOnColumnSQL($tableName, $columnName, $comment)
 * @method bool onSchemaAlterTableRemoveColumn(Column $column, TableDiff $diff, &$columnSql)
 * @method bool SchemaAlterTableChangeColumn(ColumnDiff $columnDiff, TableDiff $diff, &$columnSql)
 * @method string getDefaultValueDeclarationSQL($field)
 * @method getIdentitySequenceName($tableName, $columnName)
 * @method bool onSchemaAlterTableChangeColumn(ColumnDiff $columnDiff, TableDiff $diff, &$columnSql)
 * @method string[] getPreAlterTableIndexForeignKeySQL(TableDiff $diff)
 * @method string[] getPostAlterTableIndexForeignKeySQL(TableDiff $diff)
 * @method bool onSchemaAlterTable(TableDiff $diff, &$sql)
 * @method bool onSchemaAlterTableRenameColumn($oldColumnName, Column $column, TableDiff $diff, &$columnSql)
 */
trait AlterTableSQLDeclarations
{
    /**
     * @codeCoverageIgnore
     */
    public function getAlterTableAddColumnSQL(string $table, string $column): string
    {
        return sprintf('ALTER TABLE %s ADD %s', $table, $column);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getAlterTableDropColumnSQL(string $table, string $column): string
    {
        return sprintf('ALTER TABLE %s DROP %s', $table, $column);
    }

    public function getAlterTableAlterTypeColumnSQL(
        string $table,
        string $column,
        string $type,
        array $columnDefinition
    ): string {
        $using = '';
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

    public function getAlterTableRenameColumnSQL(string $table, string $oldColumn, string $newColumn): string
    {
        return sprintf('ALTER TABLE %s RENAME COLUMN %s TO %s', $table, $oldColumn, $newColumn);
    }

    public function getAlterTableAlterNotNullSQL(string $table, string $columnName, Column $column): string
    {
        return sprintf(
            'ALTER TABLE %s ALTER %s %s NOT NULL',
            $table,
            $columnName,
            $column->getNotnull() ? 'SET' : 'DROP'
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function getAlterTableRenameToSQL(string $table, string $name): string
    {
        return sprintf('ALTER TABLE %s RENAME TO %s', $table, $name);
    }

    public function getSetSequenceValueSQL(string $table, string $column, string $sequence): string
    {
        return sprintf("SELECT setval('%s', (SELECT MAX(%s) FROM %s))", $sequence, $column, $table);
    }

    public function getCreateSimpleSequenceSQL(string $sequence): string
    {
        return sprintf('CREATE SEQUENCE %s', $sequence);
    }

    public function getAlterTableSQL(TableDiff $diff)
    {
        $sql = [];
        $commentsSQL = [];
        $columnSql = [];

        foreach ($diff->addedColumns as $column) {
            // @codeCoverageIgnoreStart
            if ($this->onSchemaAlterTableAddColumn($column, $diff, $columnSql)) {
                continue;
            }

            $sql[] = $this->getAlterTableAddColumnSQL(
                $diff->getName($this)->getQuotedName($this),
                $this->getColumnDeclarationSQL($column->getQuotedName($this), $column->toArray())
            );

            $comment = $this->getColumnComment($column);

            if ($comment === null || $comment === '') {
                continue;
            }

            $commentsSQL[] = $this->getCommentOnColumnSQL(
                $diff->getName($this)->getQuotedName($this),
                $column->getQuotedName($this),
                $comment
            );
            // @codeCoverageIgnoreEnd
        }

        foreach ($diff->removedColumns as $column) {
            // @codeCoverageIgnoreStart
            if ($this->onSchemaAlterTableRemoveColumn($column, $diff, $columnSql)) {
                continue;
            }

            $sql[] = $this->getAlterTableDropColumnSQL(
                $diff->getName($this)->getQuotedName($this),
                $column->getQuotedName($this)
            );
            // @codeCoverageIgnoreEnd
        }

        foreach ($diff->changedColumns as $columnDiff) {
            if ($this->onSchemaAlterTableChangeColumn($columnDiff, $diff, $columnSql)) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }

            if ($this->isUnchangedBinaryColumn($columnDiff)) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }

            $oldColumnName = $columnDiff->getOldColumnName()->getQuotedName($this);
            $column = $columnDiff->column;

            if ($columnDiff->hasChanged('type')
                || $columnDiff->hasChanged('precision')
                || $columnDiff->hasChanged('scale')
                || $columnDiff->hasChanged('fixed')
            ) {
                $type = $column->getType();

                $columnDefinition = $column->toArray();
                $columnDefinition['autoincrement'] = false;

                $sql[] = $this->getAlterTableAlterTypeColumnSQL(
                    $diff->getName($this)->getQuotedName($this),
                    $oldColumnName,
                    $type->getSQLDeclaration($columnDefinition, $this),
                    $columnDefinition
                );
            }

            if ($columnDiff->hasChanged('notnull')) {
                $sql[] = $this->getAlterTableAlterNotNullSQL(
                    $diff->getName($this)->getQuotedName($this),
                    $oldColumnName,
                    $column
                );
            }

            if ($columnDiff->hasChanged('default')
                || $this->typeChangeBreaksDefaultValue($columnDiff)
            ) {
                $sql[] = $this->getAlterTableAlterDefaultSQL(
                    $diff->getName($this)->getQuotedName($this),
                    $oldColumnName,
                    $column
                );
            }

            if ($columnDiff->hasChanged('autoincrement')) {
                if ($column->getAutoincrement()) {
                    $seqName = $this->getIdentitySequenceName($diff->name, $oldColumnName);

                    $sql[] = $this->getCreateSimpleSequenceSQL($seqName);
                    $sql[] = $this->getSetSequenceValueSQL(
                        $diff->getName($this)->getQuotedName($this),
                        $oldColumnName,
                        $seqName
                    );
                    $sql[] = $this->getAlterTableAlterDefaultSQL(
                        $diff->getName($this)->getQuotedName($this),
                        $oldColumnName,
                        $column->setDefault(new Expression("nextval('{$seqName}')"))
                    );
                } else {
                    $sql[] = $this->getAlterTableAlterDefaultSQL(
                        $diff->getName($this)->getQuotedName($this),
                        $oldColumnName,
                        $column->setDefault(null)
                    );
                }
            }

            $newComment = $this->getColumnComment($column);
            $oldComment = $this->getOldColumnComment($columnDiff);

            if ($columnDiff->hasChanged('comment')
                || ($columnDiff->fromColumn !== null && $oldComment !== $newComment)
            ) {
                $commentsSQL[] = $this->getCommentOnColumnSQL(
                    $diff->getName($this)->getQuotedName($this),
                    $column->getQuotedName($this),
                    $newComment
                );
            }

            if (!$columnDiff->hasChanged('length')) {
                continue;
            }

            $sql[] = $this->getAlterTableAlterTypeColumnSQL(
                $diff->getName($this)->getQuotedName($this),
                $oldColumnName,
                $column->getType()->getSQLDeclaration($column->toArray(), $this),
                $column->toArray()
            );
        }

        foreach ($diff->renamedColumns as $oldColumnName => $column) {
            if ($this->onSchemaAlterTableRenameColumn($oldColumnName, $column, $diff, $columnSql)) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }

            $oldColumnName = new Identifier($oldColumnName);

            $sql[] = $this->getAlterTableRenameColumnSQL(
                $diff->getName($this)->getQuotedName($this),
                $oldColumnName->getQuotedName($this),
                $column->getQuotedName($this)
            );
        }

        $tableSql = [];

        if (!$this->onSchemaAlterTable($diff, $tableSql)) {
            $sql = array_merge($sql, $commentsSQL);

            $newName = $diff->getNewName();

            if ($newName !== false) {
                // @codeCoverageIgnoreStart
                $sql[] = $this->getAlterTableRenameToSQL(
                    $diff->getName($this)->getQuotedName($this),
                    $newName->getQuotedName($this)
                );
                // @codeCoverageIgnoreEnd
            }

            $sql = array_merge(
                $this->getPreAlterTableIndexForeignKeySQL($diff),
                $sql,
                $this->getPostAlterTableIndexForeignKeySQL($diff)
            );
        }

        return array_merge($sql, $tableSql, $columnSql);
    }

    /**
     * @codeCoverageIgnore
     */
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

    /**
     * @codeCoverageIgnore
     */
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
