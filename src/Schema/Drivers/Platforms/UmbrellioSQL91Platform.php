<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers\Platforms;

use Doctrine\DBAL\Platforms\PostgreSQL91Platform;
use Umbrellio\Postgres\Schema\Drivers\Traits\AlterTableSQLDeclarations;

class UmbrellioSQL91Platform extends PostgreSQL91Platform
{
    use AlterTableSQLDeclarations;
}
