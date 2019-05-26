<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;

Blueprint::macro('attachPartition', function (string $partition) {
    return $this->addCommand('attachPartition', compact('partition'));
});

Blueprint::macro('detachPartition', function (string $partition) {
    return $this->addCommand('detachPartition', compact('partition'));
});
