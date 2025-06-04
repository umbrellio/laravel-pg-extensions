<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Helpers;

use Illuminate\Support\Facades\Log;

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
        $input1 = preg_replace_callback('/[\x00-\x1F]/', function ($match) {
            $ord = ord($match[0]);
            return in_array($ord, [9, 10, 13], true) ? $match[0] : '';
        }, $input);

        return str_replace(array("\x00", "\0"), '', $input1);
    }
}
