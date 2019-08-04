# Laravel PG extensions

[![Build Status](https://travis-ci.org/umbrellio/laravel-pg-extensions.svg?branch=master)](https://travis-ci.org/umbrellio/laravel-pg-extensions)
[![Coverage Status](https://coveralls.io/repos/github/umbrellio/laravel-pg-extensions/badge.svg?branch=master)](https://coveralls.io/github/umbrellio/laravel-pg-extensions?branch=master)

This project extends Laravel`s database layer to allow use specific Postgres features without raw queries. 

## Installation

Run this command to install:
```bash
php composer.phar require umbrellio/laravel-pg-extensions
```

## Features

 - [Extended `Schema::create()`](#extended-table-creation)
 - [Extended `Schema` with GIST/GIN indexes](#create-gist/gin-indexes)
 - [Extended `Schema` with USING](#extended-schema-using)
 - [Extended `Schema` for views](#create-views)
 - [Working with unique indexes](#extended-unique-indexes-creation)
 - [Working with partitions](#partitions)
 - [Check existing index before manipulation](#check-existing-index)

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
        ->using("('[' || number || ']')::character varyiing")
        ->change();
});
```

### Create gist/gin indexes

```php
Schema::create('table', function (Blueprint $table) {
    $table->gist(['column1', 'column2']); 
    $table->gin('column1');
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
        ->createView('active_users', , "SELECT * FROM users WHERE active = 1")
        ->materialize();
});
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
            Blueprint::class => SomeBlueprint::class,
            PostgresConnection::class => SomeConnection::class,
            PostgresGrammar::class => SomeSchemaGrammar::class,
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

Created by Vitaliy Lazeev.

## Contributing

- Fork it ( https://github.com/umbrellio/laravel-pg-extensions )
- Create your feature branch (`git checkout -b feature/my-new-feature`)
- Commit your changes (`git commit -am 'Add some feature'`)
- Push to the branch (`git push origin feature/my-new-feature`)
- Create new Pull Request

<a href="https://github.com/umbrellio/">
<img style="float: left;" src="https://umbrellio.github.io/Umbrellio/supported_by_umbrellio.svg" alt="Supported by Umbrellio" width="439" height="72">
</a>
