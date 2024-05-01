<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Unit\Schema\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\Attributes\Test;
use Umbrellio\Postgres\Schema\Types\TsTzRangeType;
use Umbrellio\Postgres\Tests\TestCase;

class TsTzRangeTypeTest extends TestCase
{
    private AbstractPlatform $abstractPlatform;

    private Type $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = new TsTzRangeType();
        $this->abstractPlatform = $this
            ->getMockBuilder(PostgreSQLPlatform::class)
            ->getMock();
    }

    #[Test]
    public function getSQLDeclaration(): void
    {
        $this->assertSame(TsTzRangeType::TYPE_NAME, $this->type->getSQLDeclaration([], $this->abstractPlatform));
    }

    #[Test]
    public function getTypeName(): void
    {
        $this->assertSame(TsTzRangeType::TYPE_NAME, $this->type->getName());
    }
}
