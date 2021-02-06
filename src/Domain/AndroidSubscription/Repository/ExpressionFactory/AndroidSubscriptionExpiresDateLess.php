<?php

namespace AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory;

use DateTimeInterface;
use Doctrine\ORM\Query\Expr;

final class AndroidSubscriptionExpiresDateLess
{
    public static function create(string $tableAlias, DateTimeInterface $purchaseDate): Expr\Comparison
    {
        $expressionBuilder = new Expr();

        return $expressionBuilder->lt($tableAlias.'.expiryTimeUTC', $expressionBuilder->literal($purchaseDate->format('Y-m-d H:i:s')));
    }
}
