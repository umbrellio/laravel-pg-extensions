<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Illuminate\Support\Traits\Macroable;
use Umbrellio\Postgres\Schema\Builder;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;

class PostgresConnection extends BasePostgresConnection
{
    use Macroable;

    public function getSchemaBuilder()
    {
        if ($this->schemaGrammar === null) {
            $this->useDefaultSchemaGrammar();
        }
        return new Builder($this);
    }

    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new PostgresGrammar());
    }
}
