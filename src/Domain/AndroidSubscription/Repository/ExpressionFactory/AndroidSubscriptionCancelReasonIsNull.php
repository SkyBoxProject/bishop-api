<?php

namespace AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory;

use Doctrine\ORM\Query\Expr;

final class AndroidSubscriptionCancelReasonIsNull
{
    public static function create(string $tableAlias): string
    {
        $expressionBuilder = new Expr();

        return $expressionBuilder->isNull(sprintf('%s.cancelReason', $tableAlias));
    }
}
