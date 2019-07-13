<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers\Platforms;

use Doctrine\DBAL\Platforms\PostgreSQL94Platform;
use Umbrellio\Postgres\Schema\Drivers\Traits\AlterTableSQLDeclarations;

class UmbrellioSQL94Platform extends PostgreSQL94Platform
{
    use AlterTableSQLDeclarations;
}
