<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Doctrine;

use Umbrellio\Postgres\Doctrine\Schema\Grammars\VectorGrammar;
use Umbrellio\Postgres\Doctrine\Schema\VectorBlueprint;
use Umbrellio\Postgres\Doctrine\Types\TsVectorType;
use Umbrellio\Postgres\Extensions\AbstractExtension;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;

class VectorExtension extends AbstractExtension
{
    private const NAME = 'vector';

    public static function getMixins(): array
    {
        return [
            Blueprint::class => VectorBlueprint::class,
            PostgresGrammar::class => VectorGrammar::class,
        ];
    }

    public static function getName(): string
    {
        return static::NAME;
    }

    public static function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            TsVectorType::TYPE_NAME => TsVectorType::class,
        ]);
    }
}
