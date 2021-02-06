<?php

namespace AppBundle\Domain\AndroidSubscription\Resolver;

use AppBundle\Domain\AndroidSubscription\Entity\AndroidSubscription;
use Money\Money;

final class AndroidSubscriptionRevenueResolver
{
    private const YEAR = 12;
    private const REVENUE_FOR_FIRST_YEAR_AS_PERCENTAGE = 0.70;
    private const REVENUE_AFTER_YEAR_AS_PERCENTAGE = 0.85;

    public function resolveFromAndroidSubscription(AndroidSubscription $androidSubscription): Money
    {
        if ($androidSubscription->isTrialPeriod()) {
            return Money::USD(0);
        }

        $revenueAsPercentage = self::calculateRevenueAsPercentage($androidSubscription->getAtPurchaseLifetimeInMonths());

        return $androidSubscription->getPricePennies()->multiply($revenueAsPercentage);
    }

    private static function calculateRevenueAsPercentage(int $atPurchaseLifetimeInMonths): float
    {
        return $atPurchaseLifetimeInMonths >= self::YEAR ? self::REVENUE_AFTER_YEAR_AS_PERCENTAGE : self::REVENUE_FOR_FIRST_YEAR_AS_PERCENTAGE;
    }
}
