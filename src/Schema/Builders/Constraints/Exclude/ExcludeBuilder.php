<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Builders\Constraints\Exclude;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Builders\WhereBuilderTrait;

class ExcludeBuilder extends Fluent
{
    use WhereBuilderTrait;

    public function method(string $method): self
    {
        $this->attributes['method'] = $method;
        return $this;
    }

    public function with(string $storageParameter, $value): self
    {
        $this->attributes['with'][$storageParameter] = $value;
        return $this;
    }

    public function tableSpace(string $tableSpace): self
    {
        $this->attributes['tableSpace'] = $tableSpace;
        return $this;
    }

    public function using(string $excludeElement, string $operator): self
    {
        $this->attributes['using'][$excludeElement] = $operator;
        return $this;
    }
}
