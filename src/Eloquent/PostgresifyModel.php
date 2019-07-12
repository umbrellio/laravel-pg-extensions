<?php

namespace Umbrellio\Postgres\Eloquent;

use Umbrellio\Postgres\Eloquent\Transformers\PostgresifyTypeTransformer;
use Illuminate\Database\Eloquent\Model;

class PostgresifyModel extends Model
{
    protected $postgresifyTypes = [];

    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);
        if (array_key_exists($key, $this->postgresifyTypes) && $value !== null) {
            return PostgresifyTypeTransformer::transform($key, $value, $this->postgresifyTypes[$key]);
        }
        return $value;
    }
}
