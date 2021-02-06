<?php

namespace AppBundle\Domain\AndroidSubscription\TransferObject;

use AppBundle\Entity\User;

final class RequestAndroidSubscriptionVerification
{
    private $productId;
    private $token;
    private $notificationType;
    private $testing;
    private $user;

    public function __construct(string $productId, string $token, int $notificationType)
    {
        $this->productId = $productId;
        $this->token = $token;
        $this->notificationType = $notificationType;

        $this->testing = false;
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

    public function isTest(): bool
    {
        return $this->testing;
    }

    public function markTest(): self
    {
        $this->testing = true;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
