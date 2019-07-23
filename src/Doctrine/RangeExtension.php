<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Doctrine;

use Umbrellio\Postgres\Doctrine\Schema\Grammars\RangeGrammar;
use Umbrellio\Postgres\Doctrine\Schema\RangeBlueprint;
use Umbrellio\Postgres\Doctrine\Types\TsRangeType;
use Umbrellio\Postgres\Extensions\AbstractExtension;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;

class RangeExtension extends AbstractExtension
{
    private const NAME = 'range';

    public static function getMixins(): array
    {
        return [
            Blueprint::class => RangeBlueprint::class,
            PostgresGrammar::class => RangeGrammar::class,
        ];
    }

    public static function getName(): string
    {
        return static::NAME;
    }

    public static function getTypes(): array
    {
        return [
            TsRangeType::TYPE_NAME => TsRangeType::class,
        ];
    }
}
