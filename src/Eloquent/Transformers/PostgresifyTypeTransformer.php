<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Eloquent\Transformers;

use Umbrellio\Postgres\Types\DateRange;

class PostgresifyTypeTransformer
{
    public static function transform(string $key, $value, array $typeInformation)
    {
        $transformMethod = 'transform' . ucfirst($typeInformation['type']);
        return self::$transformMethod($key, $value, $typeInformation);
    }

    public static function transformDateRange(string $key, $value, array $typeInformation): DateRange
    {
        return new DateRange($value);
    }
}
