<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Unit\Schema;

use Generator;
use Illuminate\Support\Carbon;
use Umbrellio\Postgres\Eloquent\PostgresifyModel;
use Umbrellio\Postgres\Tests\TestCase;

class PostgresifyModelTest extends TestCase
{
    /**
     * @dataProvider provideIntervals
     * @test
     */
    public function transform(string $interval, Carbon $periodStart, Carbon $periodEnd): void
    {
        $model = new class() extends PostgresifyModel {
            protected $postgresifyTypes = [
                'interval' => [
                    'type' => 'dateRange',
                ],
            ];

            protected $fillable = ['interval'];
        };

        $model->fill(compact('interval'));

        $this->assertSame(implode(',', [$periodStart, $periodEnd]), (string) $model->interval);
        $this->assertIsArray($model->interval->toArray());
        $this->assertCount(2, (array) $model->interval);
    }

    public function provideIntervals(): Generator
    {
        yield ['2019-01-01,2019-01-31', Carbon::create(2019)->firstOfMonth(), Carbon::create(2019)->lastOfMonth()];
        yield ['2019-01-01,2019-03-31', Carbon::create(2019)->firstOfQuarter(), Carbon::create(2019)->lastOfQuarter()];
        yield ['2019-01-01,2019-12-31', Carbon::create(2019)->firstOfYear(), Carbon::create(2019)->lastOfYear()];
    }
}
