<?php

namespace AppBundle\Domain\AndroidSubscription\Query\Handler;

use AppBundle\Domain\AndroidSubscription\Entity\AndroidSubscription;
use AppBundle\Domain\AndroidSubscription\Exception\AndroidSubscriptionNotFoundException;
use AppBundle\Domain\AndroidSubscription\Query\GetAndroidSubscriptionByOrderIdQuery;
use AppBundle\Domain\AndroidSubscription\Repository\AndroidSubscriptionRepository;

final class GetAndroidSubscriptionByOrderIdHandler
{
    private $androidSubscriptionRepository;

    public function __construct(AndroidSubscriptionRepository $androidSubscriptionRepository)
    {
        $this->androidSubscriptionRepository = $androidSubscriptionRepository;
    }

    public function handle(GetAndroidSubscriptionByOrderIdQuery $query): AndroidSubscription
    {
        $subscription = $this->androidSubscriptionRepository->findByOrderId($query->getOrderId());

        if ($subscription === null) {
            throw new AndroidSubscriptionNotFoundException();
        }

        return $subscription;
    }
}
