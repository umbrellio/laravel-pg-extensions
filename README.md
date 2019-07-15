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
 - [Working with unique indexes](#extended-unique-indexes-creation)
 - [Working with partitions](#partitions)
 - [Create custom extensions for PostgreSQL](#custom-extensions)

### Extended table creation

Example:
```php
Schema::create('table', function (Blueprint $table) {
    $table->like('other_table')->includingAll(); 
    $table->ifNotExists();
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

### Custom Extensions

1. Create a repository for your extension, ex: `Some`
2. Add this package as a dependency in composer, ex:
   `composer require umbrellio/laravel-pg-extensions 2.*`
3. Inherit the classes you intend to extend from abstract classes with namespace: 
   `namespace Umbrellio\Postgres\Schema\Extensions`
4. Implement extension methods in closures, example:

```php
   use Umbrellio\Postgres\Schema\Extensions\AbstractBlueprint;
   
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

5. Create Extension class and mix these methods using the following syntax, ex:

```php

use Umbrellio\Postgres\PostgresConnection;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;
use Umbrellio\Postgres\Schema\Extensions\AbstractExtension;

class SomeExtension extends AbstractExtension
{
    public const NAME = 'some';

    protected static $mixins = [
        Blueprint::class => SomeBlueprint::class,
        PostgresConnection::class => SomeConnection::class,
        PostgresGrammar::class => SomeSchemaGrammar::class,
    ];
}
```

6. Register your ServiceProvider in the app, ex:
```php
use Illuminate\Support\ServiceProvider;

class SomeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        SomeExtension::register();
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
