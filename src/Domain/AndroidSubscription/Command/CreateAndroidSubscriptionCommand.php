<?php

namespace AppBundle\Domain\AndroidSubscription\Command;

use AppBundle\Entity\User;
use Google_Service_AndroidPublisher_SubscriptionPurchase as AndroidPurchase;

final class CreateAndroidSubscriptionCommand extends CommonAndroidSubscriptionCommand
{
    private $payload;
    private $user;

    public function __construct(
        AndroidPurchase $payload,
        string $productId,
        string $token,
        int $notificationType,
        ?User $user
    ) {
        parent::__construct($productId, $token, $notificationType);

        $this->payload = $payload;
        $this->user = $user;
    }

    public function getPayload(): AndroidPurchase
    {
        return $this->payload;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
