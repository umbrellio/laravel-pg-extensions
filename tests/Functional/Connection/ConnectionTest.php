<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Tests\Functional\Connection;

use Illuminate\Database\Connection;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Umbrellio\Postgres\Connectors\ConnectionFactory;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Tests\_data\CustomSQLiteConnection;
use Umbrellio\Postgres\Tests\FunctionalTestCase;

class ConnectionTest extends FunctionalTestCase
{
    use DatabaseTransactions, InteractsWithDatabase;

    protected $emulatePrepares = true;

    /**
     * @test
     */
    public function connectionFactory(): void
    {
        $factory = new ConnectionFactory(app());

        $this->assertInstanceOf(SQLiteConnection::class, $factory->make(config('database.connections.sqlite')));
    }

    /**
     * @test
     */
    public function resolverFor(): void
    {
        Connection::resolverFor('sqlite', function ($connection, $database, $prefix, $config) {
            return new CustomSQLiteConnection($connection, $database, $prefix, $config);
        });

        $factory = new ConnectionFactory(app());

        $this->assertInstanceOf(
            CustomSQLiteConnection::class,
            $factory->make(config('database.connections.sqlite'))
        );
    }

    /**
     * @test
     * @dataProvider boolDataProvider
     */
    public function boolTrueBindingsWorks($value)
    {
        $table = 'test_table';
        $data = [
            'field' => $value,
        ];
        Schema::create($table, function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('field');
        });
        DB::table($table)->insert($data);
        $result = DB::table($table)->select($data);
        $this->assertSame(1, $result->count());
    }

    /**
     * @test
     * @dataProvider intDataProvider
     */
    public function intBindingsWorks($value)
    {
        $table = 'test_table';
        $data = [
            'field' => $value,
        ];
        Schema::create($table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('field');
        });
        DB::table($table)->insert($data);
        $result = DB::table($table)->select($data);
        $this->assertSame(1, $result->count());
    }

    /**
     * @test
     */
    public function stringBindingsWorks()
    {
        $table = 'test_table';
        $data = [
            'field' => 'string',
        ];
        Schema::create($table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('field');
        });
        DB::table($table)->insert($data);
        $result = DB::table($table)->select($data);
        $this->assertSame(1, $result->count());
    }

    /**
     * @test
     */
    public function nullBindingsWorks()
    {
        $table = 'test_table';
        $data = [
            'field' => null,
        ];
        Schema::create($table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('field')
                ->nullable();
        });
        DB::table($table)->insert($data);
        $result = DB::table($table)->whereNull('field')->get();
        $this->assertSame(1, $result->count());
    }

    /**
     * @test
     * @dataProvider dateDataProvider
     */
    public function dateTimeBindingsWorks($value)
    {
        $table = 'test_table';
        $data = [
            'field' => $value,
        ];
        Schema::create($table, function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('field');
        });
        DB::table($table)->insert($data);
        $result = DB::table($table)->select($data);
        $this->assertSame(1, $result->count());
    }

    public function boolDataProvider()
    {
        yield 'true' => [true];
        yield 'false' => [false];
    }

    public function intDataProvider()
    {
        yield 'zero' => [0];
        yield 'non-zero' => [10];
    }

    public function dateDataProvider()
    {
        yield 'as string' => ['2019-01-01 13:12:22'];
        yield 'as Carbon object' => [new Carbon('2019-01-01 13:12:22')];
    }
}
