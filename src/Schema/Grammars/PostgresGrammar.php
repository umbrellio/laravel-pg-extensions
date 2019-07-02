<?php

namespace Umbrellio\Postgres\Schema\Grammars;

use Illuminate\Support\Carbon;
use Illuminate\Database\Schema\Grammars\PostgresGrammar as BasePostgresGrammar;
use InvalidArgumentException;
use Umbrellio\Postgres\Definitions\LikeDefinition;
use Illuminate\Support\Fluent;
use Umbrellio\Postgres\Schema\Blueprint;

class PostgresGrammar extends BasePostgresGrammar
{
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
    
    /**
     * @param Blueprint $blueprint
     * @param Fluent $command
     * @return string
     */
    public function compileCreate($blueprint, $command): string
    {
        return sprintf('%s table %s %s (%s)',
            $blueprint->temporary ? 'create temporary' : 'create',
            $this->getIfNotExists($this->getCommandByName($blueprint, 'ifNotExists')),
            $this->wrapTable($blueprint),
            ($likeCommand = $this->getCommandByName($blueprint, 'like'))
                ? $this->getLikeColumns($likeCommand)
                : implode(', ', $this->getColumns($blueprint))
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
     * @return string
     */
    private function formatValue($date): string
    {
        if ($date instanceof Carbon) {
            return "'{$date->toDateTimeString()}'";
        }

        return $date;
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
}
