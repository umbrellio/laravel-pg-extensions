<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Doctrine\Schema;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Doctrine\Types\TsVectorType;
use Umbrellio\Postgres\Extensions\Schema\AbstractBlueprint;

class VectorBlueprint extends AbstractBlueprint
{
    public function tsVector()
    {
        return function (string $column): Fluent {
            return $this->addColumn(TsVectorType::TYPE_NAME, $column);
        };
    }
}
