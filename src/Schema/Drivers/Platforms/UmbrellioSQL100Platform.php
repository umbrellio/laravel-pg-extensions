<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers\Platforms;

use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Umbrellio\Postgres\Schema\Drivers\Traits\AlterTableSQLDeclarations;

class UmbrellioSQL100Platform extends PostgreSQL100Platform
{
    use AlterTableSQLDeclarations;
}
