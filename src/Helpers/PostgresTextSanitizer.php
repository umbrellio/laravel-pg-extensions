<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Helpers;

class PostgresTextSanitizer
{
    /**
     * Удаляет запрещённые/опасные символы из строки для PostgreSQL.
     *
     * Удаляются:
     * - \x00 (нулевой байт)
     * - управляющие символы ASCII от \x01 до \x1F, кроме \x09, \x0A, \x0D
     */
    public static function sanitize(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        // Удаляем управляющие символы, кроме: табуляция (\x09), \n (\x0A), \r (\x0D)
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $input);

        return $input;
    }
}
