<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers\Platforms;

use Doctrine\DBAL\Platforms\Keywords\PostgreSQL91Keywords;

class SQL91Platform extends SQLPlatform
{
    public function supportsColumnCollation()
    {
        return true;
    }

    public function getColumnCollationDeclarationSQL($collation)
    {
        return 'COLLATE ' . $this->quoteSingleIdentifier($collation);
    }

    public function getListTableColumnsSQL($table, $database = null)
    {
        $sql = parent::getListTableColumnsSQL($table, $database);
        $parts = explode('AS complete_type,', $sql, 2);

        return $parts[0] . 'AS complete_type, (SELECT tc.collcollate FROM pg_catalog.pg_collation tc WHERE tc.oid = a.attcollation) AS collation,' . $parts[1];
    }

    protected function getReservedKeywordsClass()
    {
        return PostgreSQL91Keywords::class;
    }
}
