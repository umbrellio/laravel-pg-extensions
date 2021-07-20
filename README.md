# Laravel PG extensions

[![Github Status](https://github.com/umbrellio/laravel-pg-extensions/workflows/CI/badge.svg)](https://github.com/umbrellio/laravel-pg-extensions/actions)
[![Coverage Status](https://coveralls.io/repos/github/umbrellio/laravel-pg-extensions/badge.svg?branch=master)](https://coveralls.io/github/umbrellio/laravel-pg-extensions?branch=master)
[![Latest Stable Version](https://poser.pugx.org/umbrellio/laravel-pg-extensions/v/stable.png)](https://packagist.org/packages/umbrellio/laravel-pg-extensions)
[![Total Downloads](https://poser.pugx.org/umbrellio/laravel-pg-extensions/downloads.png)](https://packagist.org/packages/umbrellio/laravel-pg-extensions)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/umbrellio/laravel-pg-extensions/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Build Status](https://scrutinizer-ci.com/g/umbrellio/laravel-pg-extensions/badges/build.png?b=master)](https://scrutinizer-ci.com/g/umbrellio/laravel-pg-extensions/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/umbrellio/laravel-pg-extensions/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/umbrellio/laravel-pg-extensions/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/umbrellio/laravel-pg-extensions/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/umbrellio/laravel-pg-extensions/?branch=master)

This project extends Laravel's database layer to allow use specific Postgres features without raw queries. 

## Installation

Run this command to install:
```bash
composer require umbrellio/laravel-pg-extensions
```

## Features

 - [Extended `Schema::create()`](#extended-table-creation)
 - [Added Support NUMERIC Type](#numeric-column-type)
 - [Extended `Schema` with USING](#extended-schema-using)
 - [Extended `Schema` for views](#create-views)
 - [Working with UNIQUE indexes](#extended-unique-indexes-creation)
 - [Working with EXCLUDE constraints](#exclude-constraints-creation)
 - [Working with CHECK constraints](#check-constraints-creation)
 - [Working with partitions](#partitions)
 - [Check existing index before manipulation](#check-existing-index)
 - [Getting foreign keys for table](#get-foreign-keys)

### Extended table creation

Example:
```php
Schema::create('table', function (Blueprint $table) {
    $table->like('other_table')->includingAll(); 
    $table->ifNotExists();
});
```

### Extended Schema USING

Example:
```php
Schema::create('table', function (Blueprint $table) {
    $table->integer('number');
});

//modifications with data...

Schema::table('table', function (Blueprint $table) {
    $table
        ->string('number')
        ->using("('[' || number || ']')::character varying")
        ->change();
});
```

### Create views

Example:
```php
// Facade methods:
Schema::createView('active_users', "SELECT * FROM users WHERE active = 1");
Schema::dropView('active_users')

// Schema methods:
Schema::create('users', function (Blueprint $table) {
    $table
        ->createView('active_users', "SELECT * FROM users WHERE active = 1")
        ->materialize();
});
```

### Get foreign keys

Example:
```php
// Facade methods:
/** @var ForeignKeyDefinition[] $fks */
$fks = Schema::getForeignKeys('some_table');

foreach ($fks as $fk) {
    // $fk->source_column_name
    // $fk->target_table_name
    // $fk->target_column_name
}
```

### Extended unique indexes creation

Example:
```php
Schema::create('table', function (Blueprint $table) {
    $table->string('code'); 
    $table->softDeletes();
    $table->uniquePartial('code')->whereNull('deleted_at');
});
```

If you want to delete partial unique index, use this method:
```php
Schema::create('table', function (Blueprint $table) {
    $table->dropUniquePartial(['code']);
});
```

`$table->dropUnique()` doesn't work for Partial Unique Indexes, because PostgreSQL doesn't
define a partial (ie conditional) UNIQUE constraint. If you try to delete such a Partial Unique
Index you will get an error.

```SQL
CREATE UNIQUE INDEX CONCURRENTLY examples_new_col_idx ON examples (new_col);
ALTER TABLE examples
    ADD CONSTRAINT examples_unique_constraint USING INDEX examples_new_col_idx;
```

When you create a unique index without conditions, PostgresSQL will create Unique Constraint
automatically for you, and when you try to delete such an index, Constraint will be deleted 
first, then Unique Index. 

### Exclude constraints creation

Using the example below:
```php
Schema::create('table', function (Blueprint $table) {
    $table->integer('type_id'); 
    $table->date('date_start'); 
    $table->date('date_end'); 
    $table->softDeletes();
    $table
        ->exclude(['date_start', 'date_end'])
        ->using('type_id', '=')
        ->using('daterange(date_start, date_end)', '&&')
        ->method('gist')
        ->with('some_arg', 1)
        ->with('any_arg', 'some_value')
        ->whereNull('deleted_at');
});
```

An Exclude Constraint will be generated for your table:
```SQL
ALTER TABLE test_table
    ADD CONSTRAINT test_table_date_start_date_end_excl
        EXCLUDE USING gist (type_id WITH =, daterange(date_start, date_end) WITH &&)
        WITH (some_arg = 1, any_arg = 'some_value')
        WHERE ("deleted_at" is null)
```

### Check constraints creation

Using the example below:
```php
Schema::create('table', function (Blueprint $table) {
    $table->integer('type_id'); 
    $table->date('date_start'); 
    $table->date('date_end'); 
    $table
        ->check(['date_start', 'date_end'])
        ->whereColumn('date_end', '>', 'date_end')
        ->whereIn('type_id', [1, 2, 3]);
});
```

An Check Constraint will be generated for your table:
```SQL
ALTER TABLE test_table
    ADD CONSTRAINT test_table_date_start_date_end_chk
        CHECK ("date_end" > "date_start" AND "type_id" IN [1, 2, 3])
```

### Partitions

Support for attaching and detaching partitions.

Example:
```php
Schema::table('table', function (Blueprint $table) {
    $table->attachPartition('partition')->range([
        'from' => now()->startOfDay(), // Carbon will be converted to date time string
        'to' => now()->tomorrow(),
    ]);
});
```

### Check existing index

```php
Schema::table('some_table', function (Blueprint $table) {
   // check unique index exists on column
   if ($table->hasIndex(['column'], true)) {
      $table->dropUnique(['column']);
   }
   $table->uniquePartial('column')->whereNull('deleted_at');
});
```

### Numeric column type
Unlike standard laravel `decimal` type, this type can be with [variable precision](https://www.postgresql.org/docs/current/datatype-numeric.html) 
```php
Schema::table('some_table', function (Blueprint $table) {
   $table->numeric('column_with_variable_precision');
   $table->numeric('column_with_defined_precision', 8);
   $table->numeric('column_with_defined_precision_and_scale', 8, 2);
});
```

## Custom Extensions

1). Create a repository for your extension.

2). Add this package as a dependency in composer.

3). Inherit the classes you intend to extend from abstract classes with namespace: `namespace Umbrellio\Postgres\Extensions`

4). Implement extension methods in closures, example:

```php
use Umbrellio\Postgres\Extensions\Schema\AbstractBlueprint;
class SomeBlueprint extends AbstractBlueprint
{
   public function someMethod()
   {
       return function (string $column): Fluent {
           return $this->addColumn('someColumn', $column);
       };
   }
}
```

5). Create Extension class and mix these methods using the following syntax, ex:

```php
use Umbrellio\Postgres\PostgresConnection;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;
use Umbrellio\Postgres\Extensions\AbstractExtension;

class SomeExtension extends AbstractExtension
{
    public static function getMixins(): array
    {
        return [
            SomeBlueprint::class => Blueprint::class,
            SomeConnection::class => PostgresConnection::class,
            SomeSchemaGrammar::class => PostgresGrammar::class,
            ...
        ];
    }
    
    public static function getTypes(): string
    {
        // where SomeType extends Doctrine\DBAL\Types\Type
        return [
            'some' => SomeType::class,
        ];
    }

    public static function getName(): string
    {
        return 'some';
    }
}
```

6). Register your Extension in ServiceProvider and put in config/app.php, ex:

```php
use Illuminate\Support\ServiceProvider;
use Umbrellio\Postgres\PostgresConnection;

class SomeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        PostgresConnection::registerExtension(SomeExtension::class);
    }
}
```

## TODO features

 - Extend `CreateCommand` with `inherits` and `partition by`
 - Extend working with partitions
 - COPY support
 - DISTINCT on specific columns
 - INSERT ON CONFLICT support
 - ...
 
## License

Released under MIT License.

## Authors

Created by Vitaliy Lazeev & Korben Dallas.

## Contributing

- Fork it ( https://github.com/umbrellio/laravel-pg-extensions )
- Create your feature branch (`git checkout -b feature/my-new-feature`)
- Commit your changes (`git commit -am 'Add some feature'`)
- Push to the branch (`git push origin feature/my-new-feature`)
- Create new Pull Request

<a href="https://github.com/umbrellio/">
<img style="float: left;" src="https://umbrellio.github.io/Umbrellio/supported_by_umbrellio.svg" alt="Supported by Umbrellio" width="439" height="72">
</a>
