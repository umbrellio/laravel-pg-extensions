<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Unit\Schema\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Umbrellio\Postgres\Schema\Types\NumericType;
use Umbrellio\Postgres\Tests\TestCase;

class NumericTypeTest extends TestCase
{
    /**
     * @var AbstractPlatform
     */
    private $abstractPlatform;

    /**
     * @var NumericType
     */
    private $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = $this
            ->getMockBuilder(NumericType::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->abstractPlatform = $this->getMockForAbstractClass(AbstractPlatform::class);
    }


    /**
     * @test
     */
    public function getSQLDeclaration(): void
    {
        $this->assertSame(NumericType::TYPE_NAME, $this->type->getSQLDeclaration([], $this->abstractPlatform));
    }


    /**
     * @test
     */
    public function getTypeName(): void
    {
        $this->assertSame(NumericType::TYPE_NAME, $this->type->getName());
    }
}
