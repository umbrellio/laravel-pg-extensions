<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Schema\Grammars;

use Illuminate\Database\Schema\Grammars\PostgresGrammar as BasePostgresGrammar;
use Illuminate\Support\Carbon;
use Illuminate\Support\Fluent;
use InvalidArgumentException;
use Umbrellio\Postgres\Schema\Blueprint;
use Umbrellio\Postgres\Schema\Definitions\LikeDefinition;

class PostgresGrammar extends BasePostgresGrammar
{
    /**
     * @param Blueprint $blueprint
     * @param Fluent $command
     * @return string
     */
    public function compileCreate($blueprint, $command): string
    {
        $likeCommand = $this->getCommandByName($blueprint, 'like');
        return sprintf('%s table %s %s (%s)',
            $blueprint->temporary ? 'create temporary' : 'create',
            $this->getIfNotExists($this->getCommandByName($blueprint, 'ifNotExists')),
            $this->wrapTable($blueprint),
            $likeCommand
                ? $this->getLikeColumns($likeCommand)
                : implode(', ', $this->getColumns($blueprint))
        );
    }

    /**
     * @param Blueprint $blueprint
     * @param Fluent $command
     * @return string
     */
    public function compileAttachPartition($blueprint, $command): string
    {
        return sprintf('alter table %s attach partition %s %s',
            $this->wrapTable($blueprint),
            $command->get('partition'),
            $this->compileForValues($command)
        );
    }

    /**
     * @param Blueprint $blueprint
     * @param Fluent $command
     * @return string
     */
    public function compileDetachPartition($blueprint, $command): string
    {
        return sprintf('alter table %s detach partition %s',
            $this->wrapTable($blueprint),
            $command->get('partition')
        );
    }

    /**
     * @param Fluent $command
     * @return string
     */
    private function getIfNotExists($command): string
    {
        return $command ? 'if not exists' : '';
    }
    /**
     * @param LikeDefinition|Fluent $command
     * @return string
     */
    private function getLikeColumns($command): string
    {
        $table = $command->get('table');
        $includingAll = $command->get('includingAll') ? ' including all' : '';
        return "like {$this->wrapTable($table)}{$includingAll}";
    }

    private function compileForValues(Fluent $command): string
    {
        if ($range = $command->get('range')) {
            $from = $this->formatValue($range['from']);
            $to = $this->formatValue($range['to']);
            return "for values from ({$from}) to ({$to})";
        }

        throw new InvalidArgumentException('Not set "for values" for attachPartition');
    }

    /**
     * @param string|Carbon $date
     * @return int|string
     */
    private function formatValue($date)
    {
        if ($date instanceof Carbon) {
            return "'{$date->toDateTimeString()}'";
        }

        return $date;
    }
}
