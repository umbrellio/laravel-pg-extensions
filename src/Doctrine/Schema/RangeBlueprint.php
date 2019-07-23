<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Doctrine\Schema;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Doctrine\Types\TsRangeType;
use Umbrellio\Postgres\Extensions\Schema\AbstractBlueprint;

class RangeBlueprint extends AbstractBlueprint
{
    public function tsRange()
    {
        return function (string $column): Fluent {
            return $this->addColumn(TsRangeType::TYPE_NAME, $column);
        };
    }
}
