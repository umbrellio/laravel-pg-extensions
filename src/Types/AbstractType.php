<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Types;

abstract class AbstractType
{
    abstract public function __toString(): string;

    abstract public function toArray(): array;
}
