<?php

declare(strict_types=1);

namespace Umbrellio\Postgres;

use Illuminate\Database\PostgresConnection as BasePostgresConnection;
use Illuminate\Support\Traits\Macroable;
use Umbrellio\Postgres\Extensions\AbstractExtension;
use Umbrellio\Postgres\Extensions\Exceptions\ExtensionInvalidException;
use Umbrellio\Postgres\Schema\Builder;
use Umbrellio\Postgres\Schema\Drivers\DoctrineDriver;
use Umbrellio\Postgres\Schema\Grammars\PostgresGrammar;

class PostgresConnection extends BasePostgresConnection
{
    use Macroable;

    private static $extensions = [];

    /**
     * @param AbstractExtension|string $extension
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

    public function useDefaultPostProcessor()
    {
        parent::useDefaultPostProcessor();
        $this->registerExtensions();
    }

    protected function getDoctrineDriver()
    {
        return new DoctrineDriver();
    }

    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new PostgresGrammar());
    }

    /**
     * @codeCoverageIgnore
     */
    final private function registerExtensions(): void
    {
        collect(self::$extensions)->each(function ($extension, $key) {
            /** @var AbstractExtension $extension */
            $extension::register();
            foreach ($extension::getTypes() as $type => $typeClass) {
                $this->getSchemaBuilder()->registerCustomDoctrineType($typeClass, $type, $type);
            }
        });
    }
}
