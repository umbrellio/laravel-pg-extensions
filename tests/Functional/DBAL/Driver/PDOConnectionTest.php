<?php

namespace Umbrellio\Postgres\Tests\Functional\DBAL\Driver;

use Doctrine\DBAL\Driver\PDOConnection;
use Doctrine\DBAL\Driver\PDOException;
use Umbrellio\Postgres\Tests\Functional\DbalFunctionalTestCase;
use function extension_loaded;

class PDOConnectionTest extends DbalFunctionalTestCase
{
    /**
     * The PDO driver connection under test.
     *
     * @var PDOConnection
     */
    protected $driverConnection;

    protected function setUp() : void
    {
        if (! extension_loaded('PDO')) {
            $this->markTestSkipped('PDO is not installed.');
        }

        parent::setUp();

        $this->driverConnection = $this->connection->getWrappedConnection();

        if ($this->driverConnection instanceof PDOConnection) {
            return;
        }

        $this->markTestSkipped('PDO connection only test.');
    }

    protected function tearDown() : void
    {
        $this->resetSharedConn();

        parent::tearDown();
    }

    public function testDoesNotRequireQueryForServerVersion() : void
    {
        self::assertFalse($this->driverConnection->requiresQueryForServerVersion());
    }

    public function testThrowsWrappedExceptionOnConstruct() : void
    {
        $this->expectException(PDOException::class);

        new PDOConnection('foo');
    }

    /**
     * @group DBAL-1022
     */
    public function testThrowsWrappedExceptionOnExec() : void
    {
        $this->expectException(PDOException::class);

        $this->driverConnection->exec('foo');
    }

    public function testThrowsWrappedExceptionOnQuery() : void
    {
        $this->expectException(PDOException::class);

        $this->driverConnection->query('foo');
    }
}
