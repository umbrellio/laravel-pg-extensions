<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TsRangeType extends Type
{
    public const TYPE_NAME = 'tsrange';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return static::TYPE_NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value;
    }

    public function getName(): string
    {
        return self::TYPE_NAME;
    }
}
