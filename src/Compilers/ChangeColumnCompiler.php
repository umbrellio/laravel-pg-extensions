<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Compilers;

use Doctrine\DBAL\Schema\Expressions\Expression;
use Illuminate\Database\Query\Expression as BaseExpression;
use Illuminate\Database\Schema\Grammars\ChangeColumn;

class ChangeColumnCompiler extends ChangeColumn
{
    protected static function mapFluentValueToDoctrine($option, $value)
    {
        if ($option === 'default' && $value instanceof BaseExpression) {
            return new Expression($value);
        }
        return parent::mapFluentValueToDoctrine($option, $value);
    }
}
