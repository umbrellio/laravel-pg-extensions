<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers;

use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Umbrellio\Postgres\Schema\Drivers\Traits\AlterTableSQLDeclarations;

class UmbrellioSqlPlatform extends PostgreSqlPlatform
{
    use AlterTableSQLDeclarations;
}
