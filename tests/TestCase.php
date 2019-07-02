<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Umbrellio\Postgres\UmbrellioPostgresProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [UmbrellioPostgresProvider::class];
    }
}
