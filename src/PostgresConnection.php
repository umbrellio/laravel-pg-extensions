<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Events;
use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Illuminate\Support\Traits\Macroable;
use PDO;
use Umbrellio\Postgres\Extensions\AbstractExtension;
use Umbrellio\Postgres\Extensions\Exceptions\ExtensionInvalidException;
use Umbrellio\Postgres\Schema\Builder;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;
use Umbrellio\Postgres\Schema\Subscribers\SchemaAlterTableChangeColumnSubscriber;

class PostgresConnection extends BasePostgresConnection
{
    use Macroable;

    private static $extensions = [];

    /**
     * @param AbstractExtension|string $extension
     * @throws ExtensionInvalidException
     * @codeCoverageIgnore
     */
    final public static function registerExtension(string $extension): void
    {
        if (!is_subclass_of($extension, AbstractExtension::class)) {
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
    }

    public function getDoctrineConnection(): Connection
    {
        $doctrineConnection = parent::getDoctrineConnection();
        $this->overrideDoctrineBehavior($doctrineConnection);
        return $doctrineConnection;
    }

    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new PostgresGrammar());
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
                $this->getSchemaBuilder()->registerCustomDoctrineType($typeClass, $type, $type);
            }
        });
    }

    private function overrideDoctrineBehavior(Connection $connection): Connection
    {
        $eventManager = $connection->getEventManager();
        if (!$eventManager->hasListeners(Events::onSchemaAlterTableChangeColumn)) {
            $eventManager->addEventSubscriber(new SchemaAlterTableChangeColumnSubscriber());
        }
        $connection->getDatabasePlatform()->setEventManager($eventManager);
        return $connection;
    }

    public function bindValues($statement, $bindings)
    {
        if ($this->pdo->getAttribute(PDO::ATTR_EMULATE_PREPARES)) {
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
        if ($this->pdo->getAttribute(PDO::ATTR_EMULATE_PREPARES)) {
            $grammar = $this->getQueryGrammar();

            foreach ($bindings as $key => $value) {
                if ($value instanceof DateTimeInterface) {
                    $bindings[$key] = $value->format($grammar->getDateFormat());
                }
            }

            return $bindings;
        }

        return parent::prepareBindings($bindings);
    }
}
