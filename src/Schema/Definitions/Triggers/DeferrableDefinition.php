<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Definitions\Triggers;

use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Definitions\TriggerDefinition;

/**
 * @method TriggerDefinition immediate()
 * @method TriggerDefinition deferred()
 */
class DeferrableDefinition extends Fluent
{
}
