<?php

namespace Umbrellio\Postgres\Tests\Functional\DBAL\Platform;

use Umbrellio\Postgres\Tests\Functional\DbalFunctionalTestCase;

class QuotingTest extends DbalFunctionalTestCase
{
    /**
     * @dataProvider stringLiteralProvider
     */
    public function testQuoteStringLiteral(string $string) : void
    {
        $platform = $this->connection->getDatabasePlatform();
        $query    = $platform->getDummySelectSQL(
            $platform->quoteStringLiteral($string)
        );

        self::assertSame($string, $this->connection->fetchColumn($query));
    }

    /**
     * @return mixed[][]
     */
    public static function stringLiteralProvider() : iterable
    {
        return [
            'backslash' => ['\\'],
            'single-quote' => ["'"],
        ];
    }
}
