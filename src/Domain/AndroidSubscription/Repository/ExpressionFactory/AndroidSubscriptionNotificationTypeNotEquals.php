<?php

namespace AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory;

use Doctrine\ORM\Query\Expr;

final class AndroidSubscriptionNotificationTypeNotEquals
{
    public static function create(string $tableAlias, int $notificationType): Expr\Comparison
    {
        $expressionBuilder = new Expr();

        return $expressionBuilder->neq($tableAlias.'.notificationType', $expressionBuilder->literal($notificationType));
    }
}
