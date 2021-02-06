<?php

namespace AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory;

use Doctrine\ORM\Query\Expr;

final class OrderByExpiryTimeMillisDesc
{
    public static function create(string $tableAlias): Expr\OrderBy
    {
        return new Expr\OrderBy($tableAlias.'.expiryTimeMillis', 'DESC');
    }
}
