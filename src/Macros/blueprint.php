<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;

Blueprint::macro('detachPartition', function (string $partition) {
    return $this->addCommand('detachPartition', compact('partition'));
});

