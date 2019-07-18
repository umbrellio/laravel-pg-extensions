<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests;

use Illuminate\Support\Facades\Facade;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Umbrellio\Postgres\UmbrellioPostgresProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        if (!$this->app) {
            putenv('APP_ENV=testing');
            $this->app = $this->createApplication();
        }

        parent::setUp();

        Facade::clearResolvedInstances();
    }
    protected function getPackageProviders($app)
    {
        return [UmbrellioPostgresProvider::class];
    }
}
