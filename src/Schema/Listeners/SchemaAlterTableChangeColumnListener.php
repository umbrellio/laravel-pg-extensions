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

        $event->addSql(
            $this->getAlterTableChangeColumnSQL(
                $event->getPlatform(),
                $event->getTableDiff(),
                $event->getColumnDiff()
            )
        );
    }
}
