<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Generator;
use Umbrellio\Postgres\Doctrine\Types\TsRangeType;
use Umbrellio\Postgres\Tests\TestCase;

/**
 * @property AbstractPlatform $abstractPlatform
 * @property TsRangeType $type
 */
class TsRangeTypeTest extends TestCase
{
    private $abstractPlatform;
    private $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = $this
            ->getMockBuilder(TsRangeType::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->abstractPlatform = $this->getMockForAbstractClass(AbstractPlatform::class);
    }

    /** @test */
    public function getSQLDeclaration(): void
    {
        $this->assertSame(TsRangeType::TYPE_NAME, $this->type->getSQLDeclaration([], $this->abstractPlatform));
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
        yield ['[1352302322,1352302356]', '[1352302322,1352302356]'];
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
        yield ['[1352302322,1352302356]', '[1352302322,1352302356]'];
    }

    /** @test */
    public function getTypeName(): void
    {
        $this->assertSame(TsRangeType::TYPE_NAME, $this->type->getName());
    }
}
