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
    /** @test */
    public function registerInvalidExtension(): void
    {
        $abstractExtension = new class() extends AbstractExtension {
            public static function getName(): string
            {
                return 'extension';
            }

            public static function getMixins(): array
            {
                return [
                    Blueprint::class => new class() extends Model {
                    },
                ];
            }
        };

        $this->expectException(MixinInvalidException::class);

        /** @var AbstractExtension $abstractExtension */
        $abstractExtension::register();
    }

    /** @test */
    public function registerWithInvalidMixin(): void
    {
        $abstractExtension = new class() extends AbstractExtension {
            public static function getName(): string
            {
                return 'extension';
            }

            public static function getMixins(): array
            {
                return [
                    ServiceProvider::class => new class() extends AbstractComponent {
                    },
                ];
            }
        };

        $this->expectException(MacroableMissedException::class);

        /** @var AbstractExtension $abstractExtension */
        $abstractExtension::register();
    }
}
