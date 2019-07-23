<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Doctrine\Schema\Grammars;

use Umbrellio\Postgres\Doctrine\Types\TsVectorType;
use Umbrellio\Postgres\Extensions\Schema\Grammar\AbstractGrammar;

class VectorGrammar extends AbstractGrammar
{
    protected function typeTsvector()
    {
        return function (): string {
            return TsVectorType::TYPE_NAME;
        };
    }
}
