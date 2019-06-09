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
 - [Working with partitions](#partitions)

### Extended table creation

Anonymous function in `Schema::create` has second argument with `CreateCommand`, that allows to extend this command. 

Example:
```php
Schema::create('table', function (Blueprint $table, CreateCommand $command) {
    $command->like('other_table')->includingAll(); 
    $command->ifNotExists();
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

