<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Unit\Schema\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Umbrellio\Postgres\Schema\Types\DateRangeType;
use Umbrellio\Postgres\Tests\TestCase;

class DateRangeTypeTest extends TestCase
{
    /**
     * @var AbstractPlatform
     */
    private $abstractPlatform;

    /**
     * @var DateRangeType
     */
    private $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = $this
            ->getMockBuilder(DateRangeType::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->abstractPlatform = $this->getMockForAbstractClass(AbstractPlatform::class);
    }


    /**
     * @test
     */
    public function getSQLDeclaration(): void
    {
        $this->assertSame(DateRangeType::TYPE_NAME, $this->type->getSQLDeclaration([], $this->abstractPlatform));
    }


    /**
     * @test
     */
    public function getTypeName(): void
    {
        $this->assertSame(DateRangeType::TYPE_NAME, $this->type->getName());
    }
}
