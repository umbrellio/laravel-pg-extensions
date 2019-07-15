<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Extensions;

use Illuminate\Support\Traits\Macroable;
use Umbrellio\Postgres\Extensions\Exceptions\MacroableMissedException;
use Umbrellio\Postgres\Extensions\Exceptions\MixinInvalidException;

/**
 * @codeCoverageIgnore
 */
abstract class AbstractExtension extends AbstractComponent
{
    abstract public static function getMixins(): array;

    abstract public static function getName(): string;

    public static function getTypes(): array
    {
        return [];
    }

    final public static function register(): void
    {
        collect(static::getMixins())->each(static function ($mixin, $extension) {
            if (!is_subclass_of($mixin, AbstractComponent::class)) {
                throw new MixinInvalidException(sprintf(
                    'Mixed class %s is not descendant of %s',
                    $mixin,
                    AbstractComponent::class
                ));
            }
            if (!method_exists($extension, 'mixin')) {
                throw new MacroableMissedException(sprintf('Class %s has not using Macroable Trait', $extension));
            }
            /** @var AbstractComponent * @var Macroable $extension */
            $extension::mixin(new $mixin());
        });
    }
}
