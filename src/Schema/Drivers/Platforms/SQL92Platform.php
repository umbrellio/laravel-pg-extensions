<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Drivers\Platforms;

use Doctrine\DBAL\Platforms\Keywords\PostgreSQL92Keywords;
use Doctrine\DBAL\Types\Types;

class SQL92Platform extends SQL91Platform
{
    /**
     * {@inheritdoc}
     */
    public function getJsonTypeDeclarationSQL(array $field)
    {
        return 'JSON';
    }

    /**
     * {@inheritdoc}
     */
    public function getSmallIntTypeDeclarationSQL(array $field)
    {
        if (!empty($field['autoincrement'])) {
            return 'SMALLSERIAL';
        }

        return parent::getSmallIntTypeDeclarationSQL($field);
    }

    /**
     * {@inheritdoc}
     */
    public function hasNativeJsonType()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCloseActiveDatabaseConnectionsSQL($database)
    {
        return sprintf(
            'SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = %s',
            $this->quoteStringLiteral($database)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getReservedKeywordsClass()
    {
        return PostgreSQL92Keywords::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeDoctrineTypeMappings()
    {
        parent::initializeDoctrineTypeMappings();

        $this->doctrineTypeMapping['json'] = Types::JSON;
    }
}