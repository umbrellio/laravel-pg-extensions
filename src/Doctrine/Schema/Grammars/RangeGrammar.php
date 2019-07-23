<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Doctrine\Schema\Grammars;

use Umbrellio\Postgres\Doctrine\Types\TsRangeType;
use Umbrellio\Postgres\Extensions\Schema\Grammar\AbstractGrammar;

class RangeGrammar extends AbstractGrammar
{
    protected function typeTsrange()
    {
        return function (): string {
            return TsRangeType::TYPE_NAME;
        };
    }
}
