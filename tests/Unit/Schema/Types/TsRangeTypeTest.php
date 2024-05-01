<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Schema\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\Test;
use Umbrellio\Postgres\Schema\Types\TsRangeType;
use Umbrellio\Postgres\Tests\TestCase;

class TsRangeTypeTest extends TestCase
{
    private AbstractPlatform $abstractPlatform;

    private Type $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = new TsRangeType();
        $this->abstractPlatform = $this
            ->getMockBuilder(PostgreSQLPlatform::class)
            ->getMock();
    }

    #[Test]
    public function getSQLDeclaration(): void
    {
        $this->assertSame(TsRangeType::TYPE_NAME, $this->type->getSQLDeclaration([], $this->abstractPlatform));
    }

    #[Test]
    public function getTypeName(): void
    {
        $this->assertSame(TsRangeType::TYPE_NAME, $this->type->getName());
    }
}
