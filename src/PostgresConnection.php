<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Umbrellio\Postgres\Schema\Builder;

class PostgresConnection extends BasePostgresConnection
{
    public function getSchemaBuilder()
    {
        if ($this->schemaGrammar === null) {
            $this->useDefaultSchemaGrammar();
        }
        return new Builder($this);
    }

    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new Query\Grammars\PostgresGrammar);
    }

    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new Schema\Grammars\PostgresGrammar);
    }
}
