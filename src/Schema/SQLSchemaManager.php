<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema;

use Doctrine\DBAL\Schema\PostgreSqlSchemaManager;
use Illuminate\Support\Traits\Macroable;

class SQLSchemaManager extends PostgreSqlSchemaManager
{
    use Macroable;
}
