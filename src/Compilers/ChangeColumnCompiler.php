<?php

declare(strict_types=1);

namespace Umbrellio\Postgres\Compilers;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Grammars\ChangeColumn;

class ChangeColumnCompiler extends ChangeColumn
{
    protected static function mapFluentValueToDoctrine($option, $value)
    {
        if ($option === 'default' && $value instanceof Expression) {
            return new CompositeExpression(CompositeExpression::TYPE_AND, [$value]);
        }
        return parent::mapFluentValueToDoctrine($option, $value);
    }
}
