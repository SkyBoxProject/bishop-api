<?php

namespace AppBundle\Domain\AndroidSubscription\Resolver;

use AppBundle\Domain\AndroidSubscription\Entity\AndroidSubscription;
use AppBundle\Domain\AndroidSubscription\Repository\AndroidSubscriptionRepository;
use AppBundle\Domain\AppleTransaction\Common\TransferObject\AtPurchaseLifetime;
use Psr\Log\LoggerInterface;

/**
 * @final
 */
class AtPurchaseLifetimeResolver
{
    private const SUBSCRIPTION_LIFETIME_IN_MONTHS = [
        'monthly' => 1,
        'year7daytrial' => 12,
    ];

    private $androidSubscriptionRepository;
    private $logger;

    public function __construct(AndroidSubscriptionRepository $androidSubscriptionRepository, LoggerInterface $logger)
    {
        $this->androidSubscriptionRepository = $androidSubscriptionRepository;
        $this->logger = $logger;
    }

    public function resolveFromAndroidSubscription(AndroidSubscription $androidSubscription): AtPurchaseLifetime
    {
        $previousAndroidSubscription = $this->findPreviousExpiredAndroidSubscriptionByPurchaseDate($androidSubscription);

        if ($previousAndroidSubscription === null) {
            return self::createDefaultAtPurchaseLifetime();
        }

        if ($previousAndroidSubscription->isTrialPeriod()) {
            return new AtPurchaseLifetime($previousAndroidSubscription->getAtPurchaseLifetimeInMonths(), $previousAndroidSubscription->getAtPurchaseLifetimeInMonths());
        }

        return $this->calculateAtPurchaseLifetime($previousAndroidSubscription);
    }

    private function findPreviousExpiredAndroidSubscriptionByPurchaseDate(AndroidSubscription $androidSubscription): ?AndroidSubscription
    {
        if ($androidSubscription->getUser() === null) {
            return null;
        }

        return $this
            ->androidSubscriptionRepository
            ->findPreviousExpiredAndroidSubscriptionByPurchaseDate(
                $androidSubscription->getStartTimeUTC(),
                $androidSubscription->getUser()
            );
    }

    private static function createDefaultAtPurchaseLifetime(): AtPurchaseLifetime
    {
        return new AtPurchaseLifetime(0, 0);
    }

    private function calculateAtPurchaseLifetime(AndroidSubscription $androidSubscription): AtPurchaseLifetime
    {
        $addedAtPurchaseLifetime = $this->findAtPurchaseLifetimeFromProductId($androidSubscription->getProductId());

        $newAtPurchaseLifetime = round($androidSubscription->getAtPurchaseLifetimeInMonths() + $addedAtPurchaseLifetime, 2);

        return new AtPurchaseLifetime($androidSubscription->getAtPurchaseLifetimeInMonths(), $newAtPurchaseLifetime);
    }

    private function findAtPurchaseLifetimeFromProductId(string $productId): float
    {
        $partsProductId = explode('.', $productId);
        $productId = end($partsProductId);

        if (isset(self::SUBSCRIPTION_LIFETIME_IN_MONTHS[$productId])) {
            return self::SUBSCRIPTION_LIFETIME_IN_MONTHS[$productId];
        }

        $this->logProductIdNotFound($productId);

        return 0;
    }

    private function logProductIdNotFound(string $productId): void
    {
        $this->logger->error(sprintf('[ANDROID] Product id (%s) not found.', $productId), ['productId' => $productId]);
    }
}
