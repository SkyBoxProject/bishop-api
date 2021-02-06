<?php

namespace AppBundle\Domain\AndroidSubscription\Query;

final class GetAndroidSubscriptionByOrderIdQuery
{
    private $orderId;

    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }
}
