<?php

namespace AppBundle\Domain\AndroidSubscription\Command\Handler;

use AppBundle\Domain\AndroidSubscription\Command\CreateAndroidSubscriptionCommand;
use AppBundle\Domain\AndroidSubscription\Command\UpdateAndroidGeneralSubscriptionCommand;
use AppBundle\Domain\AndroidSubscription\Command\UpdateAndroidSubscriptionCommand;
use AppBundle\Domain\AndroidSubscription\Command\VerifyAndroidSubscriptionCommand;
use AppBundle\Domain\AndroidSubscription\Entity\AndroidSubscription;
use AppBundle\Domain\AndroidSubscription\Exception\AndroidSubscriptionNotFoundException;
use AppBundle\Domain\AndroidSubscription\Query\GetAndroidSubscriptionByOrderIdQuery;
use AppBundle\Domain\AndroidSubscription\TransferObject\RequestAndroidSubscriptionVerification;
use AppBundle\Domain\User\Exception\UserNotFoundException;
use AppBundle\Domain\User\Query\GetUserByIdQuery;
use AppBundle\Entity\User;
use AppBundle\Module\Android\Client\AndroidClient;
use AppBundle\Module\CommandBus\HasCommandBusTrait;
use AppBundle\Module\CommandBus\HasQueryBusTrait;
use Google_Service_AndroidPublisher_SubscriptionPurchase as AndroidPurchase;

final class VerifyAndroidSubscriptionHandler
{
    use HasCommandBusTrait;
    use HasQueryBusTrait;

    private $androidClient;

    public function __construct(AndroidClient $androidClient)
    {
        $this->androidClient = $androidClient;
    }

    public function handle(VerifyAndroidSubscriptionCommand $command): void
    {
        $requestVerification = $command->getRequestAndroidSubscriptionVerification();

        $androidPurchase = $this->getPurchaseFromRequestVerification($requestVerification);

        try {
            $androidSubscription = $this->getAndroidSubscription($androidPurchase->getOrderId());

            $previousUser = $androidSubscription->getUser();

            $androidSubscription = $this->updateAndroidSubscription($androidSubscription, $androidPurchase, $requestVerification);

            if ($previousUser !== null && $previousUser !== $androidSubscription->getUser()) {
                $this->updateGeneralSubscription($previousUser, $requestVerification);
            }
        } catch (AndroidSubscriptionNotFoundException $exception) {
            $androidSubscription = $this->createAndroidSubscription($androidPurchase, $requestVerification);
        }

        $this->updateGeneralSubscription($androidSubscription->getUser(), $requestVerification);
    }

    private function getPurchaseFromRequestVerification(RequestAndroidSubscriptionVerification $requestVerification): AndroidPurchase
    {
        return $this->androidClient->getPurchase($requestVerification->getProductId(), $requestVerification->getToken());
    }

    private function createAndroidSubscription(AndroidPurchase $androidPurchase, RequestAndroidSubscriptionVerification $requestVerification): AndroidSubscription
    {
        $user = $this->getUser($androidPurchase);

        $createAndroidSubscriptionCommand = new CreateAndroidSubscriptionCommand(
            $androidPurchase,
            $requestVerification->getProductId(),
            $requestVerification->getToken(),
            $requestVerification->getNotificationType(),
            $user
        );

        $this->handleCommand($createAndroidSubscriptionCommand);

        return $this->getAndroidSubscription($androidPurchase->getOrderId());
    }

    private function updateAndroidSubscription(
        AndroidSubscription $androidSubscription,
        AndroidPurchase $androidPurchase,
        RequestAndroidSubscriptionVerification $requestVerification
    ): AndroidSubscription {
        $user = $requestVerification->getUser() ?? $androidSubscription->getUser() ?? $this->getUser($androidPurchase);

        $updateAndroidSubscriptionCommand = new UpdateAndroidSubscriptionCommand(
            $androidSubscription,
            $androidPurchase,
            $requestVerification->getProductId(),
            $requestVerification->getToken(),
            $requestVerification->getNotificationType(),
            $user
        );

        $this->handleCommand($updateAndroidSubscriptionCommand);

        return $this->getAndroidSubscription($androidPurchase->getOrderId());
    }

    /**
     * @throws UserNotFoundException
     */
    private function getUser(AndroidPurchase $androidPurchase): User
    {
        $userId = (int) $androidPurchase->getObfuscatedExternalAccountId();

        return $this->handleQuery(new GetUserByIdQuery($userId));
    }

    /**
     * @throws AndroidSubscriptionNotFoundException
     */
    private function getAndroidSubscription(string $orderId): AndroidSubscription
    {
        return $this->handleQuery(new GetAndroidSubscriptionByOrderIdQuery($orderId));
    }

    private function updateGeneralSubscription(User $user, RequestAndroidSubscriptionVerification $requestVerification): void
    {
        $command = new UpdateAndroidGeneralSubscriptionCommand($user);

        if ($requestVerification->isTest()) {
            $command->markAsTest();
        }

        $this->handleCommand($command);
    }
}
