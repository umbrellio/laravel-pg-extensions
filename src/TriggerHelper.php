<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Services;

class TriggerHelper
{
    public function createStatements(
        string $tableName,
        string $name,
        string $sql,
        string $event,
        array $params = []
    ): array {
        $declare = '';
        if (count($params) > 0) {
            $declare = 'DECLARE ';
            foreach ($params as $param => $type) {
                $declare .= "\n {$param} {$type};";
            }
            $declare .= "\n";
        }
        $statements[] = "
            CREATE FUNCTION {$name}() RETURNS trigger AS \${$name}\$
            {$declare}
            BEGIN
                {$sql}
                RETURN NEW;
            END;
            \${$name}\$  LANGUAGE plpgsql
        ";
        $statements[] = "
            CREATE TRIGGER {$name} {$event} ON {$tableName}
            FOR EACH ROW EXECUTE PROCEDURE {$name}()
        ";
        return $statements;
    }

    public function dropStatements(string $name, string $tableName): array
    {
        $statements[] = 'DROP TRIGGER IF EXISTS ' . $name . ' ON ' . $tableName;
        $statements[] = 'DROP FUNCTION IF EXISTS ' . $name;
        return $statements;
    }
}
