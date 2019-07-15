<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Extensions;

use Illuminate\Support\Traits\Macroable;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractExtension
{
    protected static $mixins = [];

    final public function __construct()
    {
    }

    abstract public static function getName(): string;

    abstract public static function getTypes(): array;

    final public static function register(): void
    {
        collect(static::$mixins)->each(static function ($mixin, $extension) {
            /** @var Macroable $extension */
            $extension::mixin(new $mixin());
        });
    }
}
