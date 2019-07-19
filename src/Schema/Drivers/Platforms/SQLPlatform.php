<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers\Platforms;

use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Illuminate\Support\Traits\Macroable;
use Umbrellio\Postgres\Schema\Drivers\Traits\AlterTableSQLDeclarations;

class SQLPlatform extends PostgreSqlPlatform
{
    use AlterTableSQLDeclarations, Macroable;
}
