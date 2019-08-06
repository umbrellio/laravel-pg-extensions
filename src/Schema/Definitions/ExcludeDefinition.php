<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions;

/**
 * @method ExcludeDefinition method(string $method)
 * @method ExcludeDefinition with(string $storageParameter, $value)
 * @method ExcludeDefinition tableSpace(string $tableSpace)
 * @method ExcludeDefinition using(string $excludeElement, string $operator)
 */
class ExcludeDefinition extends AbstractWhereDefinition
{
}
