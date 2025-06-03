<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Events;
use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Traits\Macroable;
use PDO;
use Umbrellio\Postgres\Extensions\AbstractExtension;
use Umbrellio\Postgres\Extensions\Exceptions\ExtensionInvalidException;
use Umbrellio\Postgres\Helpers\PostgresTextSanitizer;
use Umbrellio\Postgres\Schema\Builder;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;
use Umbrellio\Postgres\Schema\Subscribers\SchemaAlterTableChangeColumnSubscriber;
use Umbrellio\Postgres\Schema\Types\NumericType;
use Umbrellio\Postgres\Schema\Types\TsRangeType;
use Umbrellio\Postgres\Schema\Types\TsTzRangeType;

class PostgresConnection extends BasePostgresConnection
{
    use Macroable;

    public $name;

    private static $extensions = [];

    private $initialTypes = [
        TsRangeType::TYPE_NAME => TsRangeType::class,
        TsTzRangeType::TYPE_NAME => TsTzRangeType::class,
        NumericType::TYPE_NAME => NumericType::class,
    ];

    /**
     * @param AbstractExtension|string $extension
     * @codeCoverageIgnore
     */
    final public static function registerExtension(string $extension): void
    {
        if (! is_subclass_of($extension, AbstractExtension::class)) {
            throw new ExtensionInvalidException(sprintf(
                'Class %s must be implemented from %s',
                $extension,
                AbstractExtension::class
            ));
        }
        self::$extensions[$extension::getName()] = $extension;
    }

    public function getSchemaBuilder()
    {
        if ($this->schemaGrammar === null) {
            $this->useDefaultSchemaGrammar();
        }
        return new Builder($this);
    }

    public function useDefaultPostProcessor(): void
    {
        parent::useDefaultPostProcessor();

        $this->registerExtensions();
        $this->registerInitialTypes();
    }

    public function getDoctrineConnection(): Connection
    {
        $doctrineConnection = parent::getDoctrineConnection();
        $this->overrideDoctrineBehavior($doctrineConnection);
        return $doctrineConnection;
    }

    public function bindValues($statement, $bindings)
    {
        if ($this->getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES)) {
            foreach ($bindings as $key => $value) {
                $parameter = is_string($key) ? $key : $key + 1;

                switch (true) {
                    case is_bool($value):
                        $dataType = PDO::PARAM_BOOL;
                        break;

                    case $value === null:
                        $dataType = PDO::PARAM_NULL;
                        break;

                    default:
                        $dataType = PDO::PARAM_STR;
                }

                $statement->bindValue($parameter, $value, $dataType);
            }
        } else {
            parent::bindValues($statement, $bindings);
        }
    }

    public function prepareBindings(array $bindings)
    {
        if ($this->getPdo()->getAttribute(PDO::ATTR_EMULATE_PREPARES)) {
            $grammar = $this->getQueryGrammar();

            foreach ($bindings as $key => $value) {
                if ($value instanceof DateTimeInterface) {
                    $bindings[$key] = $value->format($grammar->getDateFormat());
                }
                if (is_string($value)) {
                    $bindings[$key] = PostgresTextSanitizer::sanitize($value);
                }
            }

            return $bindings;
        }

        return parent::prepareBindings($bindings);
    }

    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new PostgresGrammar());
    }

    private function registerInitialTypes(): void
    {
        foreach ($this->initialTypes as $type => $typeClass) {
            DB::registerDoctrineType($typeClass, $type, $type);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    private function registerExtensions(): void
    {
        collect(self::$extensions)->each(function ($extension) {
            /** @var AbstractExtension $extension */
            $extension::register();
            foreach ($extension::getTypes() as $type => $typeClass) {
                DB::registerDoctrineType($typeClass, $type, $type);
            }
        });
    }

    private function overrideDoctrineBehavior(Connection $connection): Connection
    {
        $eventManager = $connection->getEventManager();
        if (! $eventManager->hasListeners(Events::onSchemaAlterTableChangeColumn)) {
            $eventManager->addEventSubscriber(new SchemaAlterTableChangeColumnSubscriber());
        }
        $connection
            ->getDatabasePlatform()
            ->setEventManager($eventManager);
        return $connection;
    }
}
