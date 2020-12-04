<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Extensions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Umbrellio\Postgres\Extensions\AbstractComponent;
use Umbrellio\Postgres\Extensions\AbstractExtension;
use Umbrellio\Postgres\Extensions\Exceptions\MacroableMissedException;
use Umbrellio\Postgres\Extensions\Exceptions\MixinInvalidException;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\TestCase;

class AbstractExtensionTest extends TestCase
{
    /**
     * @test
     */
    public function registerInvalidExtension(): void
    {
        $abstractExtension = new ExtensionStub();

        $this->expectException(MixinInvalidException::class);

        /** @var AbstractExtension $abstractExtension */
        $abstractExtension::register();
    }

    /**
     * @test
     */
    public function registerWithInvalidMixin(): void
    {
        $abstractExtension = new InvalidExtensionStub();

        $this->expectException(MacroableMissedException::class);

        /** @var AbstractExtension $abstractExtension */
        $abstractExtension::register();
    }
}

class InvalidExtensionStub extends AbstractExtension
{
    public static function getName(): string
    {
        return 'extension';
    }

    public static function getMixins(): array
    {
        return [
            ComponentStub::class => ServiceProvider::class,
        ];
    }
}

class ComponentStub extends AbstractComponent
{
}

class ExtensionStub extends AbstractExtension
{
    public static function getName(): string
    {
        return 'extension';
    }

    public static function getMixins(): array
    {
        return [
            InvalidComponentStub::class => Blueprint::class,
        ];
    }
}

class InvalidComponentStub extends Model
{
}
