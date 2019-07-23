<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Generator;
use Umbrellio\Postgres\Doctrine\Types\TsVectorType;
use Umbrellio\Postgres\Tests\TestCase;

class TsVectorTypeTest extends TestCase
{
    /** @var AbstractPlatform */
    private $abstractPlatform;

    /** @var TsVectorType */
    private $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = $this
            ->getMockBuilder(TsVectorType::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->abstractPlatform = $this->getMockForAbstractClass(AbstractPlatform::class);
    }

    /**
     * @test
     */
    public function getSQLDeclaration(): void
    {
        $this->assertSame(TsVectorType::TYPE_NAME, $this->type->getSQLDeclaration([], $this->abstractPlatform));
    }

    /**
     * @dataProvider providePHPValues
     * @test
     */
    public function convertToPHPValue($value, $expected): void
    {
        $this->assertSame($expected, $this->type->convertToDatabaseValue($value, $this->abstractPlatform));
    }

    public function provideDatabaseValues(): Generator
    {
        yield [null, null];
        yield ['key:2, key:2,3', 'key:2, key:2,3'];
    }

    /**
     * @dataProvider provideDatabaseValues
     * @test
     */
    public function convertToDatabaseValue($value, $expected): void
    {
        $this->assertSame($expected, $this->type->convertToPHPValue($value, $this->abstractPlatform));
    }

    public function providePHPValues(): Generator
    {
        yield [null, null];
        yield ['key:2, key:2,3', 'key:2, key:2,3'];
    }

    /** @test */
    public function getTypeName(): void
    {
        $this->assertSame(TsVectorType::TYPE_NAME, $this->type->getName());
    }
}
