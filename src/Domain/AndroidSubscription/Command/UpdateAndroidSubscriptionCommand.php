<?php

namespace AppBundle\Domain\AndroidSubscription\Command;

use AppBundle\Domain\AndroidSubscription\Entity\AndroidSubscription;
use AppBundle\Entity\User;
use Google_Service_AndroidPublisher_SubscriptionPurchase as AndroidPurchase;

final class UpdateAndroidSubscriptionCommand extends CommonAndroidSubscriptionCommand
{
    private $androidSubscription;
    private $payload;
    private $user;

    public function __construct(
        AndroidSubscription $androidProduct,
        AndroidPurchase $payload,
        string $productId,
        string $token,
        int $notificationType,
        User $user
    ) {
        parent::__construct($productId, $token, $notificationType);

        $this->androidSubscription = $androidProduct;
        $this->payload = $payload;
        $this->user = $user;
    }

    public function getAndroidSubscription(): AndroidSubscription
    {
        return $this->androidSubscription;
    }

    public function getPayload(): AndroidPurchase
    {
        return $this->payload;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
