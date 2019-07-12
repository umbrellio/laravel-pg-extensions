<?php

namespace Umbrellio\Postgres\Eloquent\Transformers;

use Umbrellio\Postgres\Types\DateRange;

class PostgresifyTypeTransformer
{
    public static function transform($key, $value, $typeInformation)
    {
        $transformMethod = 'transform' . ucfirst($typeInformation['type']);
        return self::$transformMethod($key, $value, $typeInformation);
    }

    public static function transformDateRange($key, $value, $typeInformation)
    {
        return new DateRange($value);
    }
}
