<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Umbrellio\Postgres\Eloquent\Transformers\PostgresifyTypeTransformer;
use Umbrellio\Postgres\Types\AbstractType;

class PostgresifyModel extends Model
{
    protected $postgresifyTypes = [];

    /**
     * @param string $key
     * @return mixed|AbstractType
     */
    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);
        if (array_key_exists($key, $this->postgresifyTypes) && $value !== null) {
            return PostgresifyTypeTransformer::transform($key, $value, $this->postgresifyTypes[$key]);
        }
        return $value;
    }
}
