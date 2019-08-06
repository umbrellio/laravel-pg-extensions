<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Builders\Indexes\Unique;

use Illuminate\Support\Fluent;

class UniqueBuilder extends Fluent
{
    public function __call($method, $parameters)
    {
        $command = new UniquePartialBuilder();
        $this->attributes['constraints'] = call_user_func_array([$command, $method], $parameters);
        return $command;
    }
}
