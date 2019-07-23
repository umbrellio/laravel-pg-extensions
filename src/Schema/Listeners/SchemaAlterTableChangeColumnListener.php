<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Listeners;

use Doctrine\DBAL\Event\SchemaAlterTableChangeColumnEventArgs;
use Umbrellio\Postgres\Schema\Traits\AlterTableChangeColumnTrait;

class SchemaAlterTableChangeColumnListener
{
    use AlterTableChangeColumnTrait;

    public function onSchemaAlterTableChangeColumn(SchemaAlterTableChangeColumnEventArgs $event): void
    {
        $event->preventDefault();

        $sql = array_unique(array_merge($event->getSql(), $this->getAlterTableChangeColumnSQL(
            $event->getPlatform(),
            $event->getTableDiff(),
            $event->getColumnDiff()
        )));

        $event->addSql($sql);
    }
}
