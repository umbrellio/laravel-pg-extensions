<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Extensions;

use Illuminate\Support\Traits\Macroable;

abstract class AbstractExtension
{
    protected static $mixins = [];

    public static function register(): void
    {
        collect(static::$mixins)->each(static function ($mixin, $extension) {
            /** @var Macroable $extension */
            $extension::mixin(new $mixin());
        });
    }
}
