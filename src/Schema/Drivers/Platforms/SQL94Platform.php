<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers\Platforms;

use Doctrine\DBAL\Platforms\Keywords\PostgreSQL94Keywords;
use Doctrine\DBAL\Types\Types;

class SQL94Platform extends SQL92Platform
{
    public function getJsonTypeDeclarationSQL(array $field)
    {
        if (!empty($field['jsonb'])) {
            return 'JSONB';
        }

        return 'JSON';
    }

    protected function getReservedKeywordsClass()
    {
        return PostgreSQL94Keywords::class;
    }

    protected function initializeDoctrineTypeMappings()
    {
        parent::initializeDoctrineTypeMappings();

        $this->doctrineTypeMapping['jsonb'] = Types::JSON;
    }
}
