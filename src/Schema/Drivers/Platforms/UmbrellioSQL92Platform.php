<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers\Platforms;

use Doctrine\DBAL\Platforms\PostgreSQL92Platform;
use Umbrellio\Postgres\Schema\Drivers\Traits\AlterTableSQLDeclarations;

class UmbrellioSQL92Platform extends PostgreSQL92Platform
{
    use AlterTableSQLDeclarations;
}
