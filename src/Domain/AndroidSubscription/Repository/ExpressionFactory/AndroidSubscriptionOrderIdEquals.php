<?php

namespace AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory;

use Doctrine\ORM\Query\Expr;

final class AndroidSubscriptionOrderIdEquals
{
    public static function create(string $tableAlias, string $orderId): Expr\Comparison
    {
        $expressionBuilder = new Expr();

        return $expressionBuilder->eq($tableAlias.'.orderId', $expressionBuilder->literal($orderId));
    }
}
