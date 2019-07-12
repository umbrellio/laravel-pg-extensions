<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Types;

use Illuminate\Support\Carbon;

class DateRange extends AbstractType
{
    public $start;
    public $end;

    public function __construct(string $value)
    {
        $interval = explode(',', $value);
        $this->start = Carbon::createFromTimestamp(strtotime($interval[0]));
        $this->end = Carbon::createFromTimestamp(strtotime($interval[1]));
    }

    public function __toString(): string
    {
        return implode(',', [$this->start, $this->end]);
    }

    public function toArray(): array
    {
        return [$this->start, $this->end];
    }
}
