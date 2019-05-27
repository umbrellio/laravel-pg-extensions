<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Umbrellio\Postgres\Commands\CreateCommand;

Blueprint::macro('attachPartition', function (string $partition) {
    return $this->addCommand('attachPartition', compact('partition'));
});

Blueprint::macro('detachPartition', function (string $partition) {
    return $this->addCommand('detachPartition', compact('partition'));
});

Blueprint::macro('createExtended', function () {
    $command = new CreateCommand(['name' => 'create']);
    $this->commands[] = $command;

    return $command;
});
