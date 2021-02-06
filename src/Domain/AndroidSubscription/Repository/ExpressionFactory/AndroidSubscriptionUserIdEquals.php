<?php

namespace AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory;

use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr;

final class AndroidSubscriptionUserIdEquals
{
    public static function create(string $tableAlias, User $user): Expr\Comparison
    {
        $expressionBuilder = new Expr();

        return $expressionBuilder->eq($tableAlias.'.user', $expressionBuilder->literal($user->getId()));
    }
}
