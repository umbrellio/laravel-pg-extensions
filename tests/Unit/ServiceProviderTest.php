<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use PHPUnit\Framework\MockObject\MockBuilder;
use Umbrellio\Postgres\Extensions\AbstractExtension;
use Umbrellio\Postgres\Extensions\Schema\AbstractBlueprint;
use Umbrellio\Postgres\PostgresConnection;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\TestCase;
use Umbrellio\Postgres\UmbrellioPostgresProvider;

class ServiceProviderTest extends TestCase
{
    /**
     * @var MockBuilder|Application
     */
    protected $applicationMock;

    /**
     * @var AbstractProviderStub
     */
    protected $serviceProvider;

    protected function setUp(): void
    {
        $this->setUpMocks();

        $this->serviceProvider = new AbstractProviderStub($this->applicationMock);

        parent::setUp();
    }

    /** @test */
    public function itCanBeConstructed(): void
    {
        $this->assertInstanceOf(ServiceProvider::class, $this->serviceProvider);
    }

    /** @test */
    public function itPerformsBootMethod(): void
    {
        $application = $this->applicationMock
            ->setMethods(['publishes', 'mergeConfigFrom'])
            ->getMock();
        $application->method('publishes')->willReturn(null);
        $application->method('mergeConfigFrom')->willReturn(null);

        $this->serviceProvider->boot();

        $this->assertTrue(true);
    }

    protected function setUpMocks(): void
    {
        $this->applicationMock = $this->getMockBuilder(Application::class);
    }
}

class AbstractRangeTypeStub extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'tsrange';
    }

    public function getName(): string
    {
        return 'tsrange';
    }
}

class AbstractExtensionStub extends AbstractExtension
{
    public static function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            'tsrange' => AbstractRangeTypeStub::class,
        ]);
    }

    public static function getMixins(): array
    {
        return [
            Blueprint::class => new class() extends AbstractBlueprint {
            },
        ];
    }

    public static function getName(): string
    {
        return 'abstractExtension';
    }
}

class AbstractProviderStub extends UmbrellioPostgresProvider
{
    public function register(): void
    {
        PostgresConnection::registerExtension(AbstractExtensionStub::class);
        parent::register();
    }
}
