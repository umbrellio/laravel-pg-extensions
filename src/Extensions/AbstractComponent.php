<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Extensions;

abstract class AbstractComponent
{
    final public function __construct()
    {
        $this->init();
    }

    protected function init(): void
    {
    }
}
