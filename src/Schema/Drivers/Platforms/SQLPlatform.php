<?php

namespace Umbrellio\Postgres\Schema\Drivers\Platforms;

use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Umbrellio\Postgres\Schema\Drivers\Traits\AlterTableSQLDeclarations;

class SQLPlatform extends PostgreSqlPlatform
{
    use AlterTableSQLDeclarations;
}
