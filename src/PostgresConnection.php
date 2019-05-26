<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\PostgresConnection as BasePostgresConnection;

class PostgresConnection extends BasePostgresConnection
{
    public function getSchemaBuilder()
    {
        if ($this->schemaGrammar === null) {
            $this->useDefaultSchemaGrammar();
        }

        return new PostgresSchemaBuilder($this);
    }
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new PostgresSchemaGrammar());
    }
}
