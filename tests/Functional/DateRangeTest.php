<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional;

use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Schema\Blueprint;

class DateRangeTest extends FunctionalTestCase
{
    /** @test */
    public function dateRange(): void
    {
        Schema::create('test_table', function (Blueprint $table) {
            $table->dateRange('interval');
        });

        $this->assertTrue(Schema::hasTable('test_table'));
        $this->assertSame(['interval'], Schema::getColumnListing('test_table'));
    }
}
