<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Definitions\Triggers\DeferrableDefinition;
use Umbrellio\Postgres\Schema\Definitions\Triggers\ExecuteDefinition;
use Umbrellio\Postgres\Schema\Definitions\Triggers\ForDefinition;
use Umbrellio\Postgres\Schema\Definitions\Triggers\ReferencingDefinition;

/**
 * @method TriggerDefinition constraint(bool $value = true)
 *
 * @method TriggerDefinition before(bool $value = true)
 * @method TriggerDefinition after(bool $value = true)
 * @method TriggerDefinition insteadOf(bool $value = true)
 * @method TriggerDefinition event($events)
 * @method TriggerDefinition notDeferrable()
 *
 * @method DeferrableDefinition deferrable()
 * @method ReferencingDefinition referencing()
 * @method ForDefinition for(bool $each = false)
 *
 * @method TriggerDefinition when(string $condition)
 * @method ExecuteDefinition execute(string $routine)
 * @method TriggerDefinition language(string $language = 'plpgsql')
 */
class TriggerDefinition extends Fluent
{
}
