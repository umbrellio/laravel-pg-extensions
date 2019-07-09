<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Builders;

use Illuminate\Support\Fluent;

class UniquePartialBuilder extends Fluent
{
    public function __call($method, $parameters)
    {
        $command = new UniqueWhereBuilder();
        $this->attributes['constraints'] = call_user_func_array([$command, $method], $parameters);
        return $command;
    }
}
