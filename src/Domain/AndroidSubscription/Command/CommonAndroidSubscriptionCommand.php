<?php

namespace AppBundle\Domain\AndroidSubscription\Command;

abstract class CommonAndroidSubscriptionCommand
{
    protected $productId;
    protected $token;
    protected $notificationType;

    public function __construct(string $productId, string $token, int $notificationType)
    {
        $this->productId = $productId;
        $this->token = $token;
        $this->notificationType = $notificationType;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getNotificationType(): int
    {
        return $this->notificationType;
    }
}
