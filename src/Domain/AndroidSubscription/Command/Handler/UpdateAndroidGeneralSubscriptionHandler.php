<?php

namespace AppBundle\Domain\AndroidSubscription\Command\Handler;

use AppBundle\Domain\AndroidSubscription\Command\UpdateAndroidGeneralSubscriptionCommand;
use AppBundle\Domain\AndroidSubscription\Exception\AndroidSubscriptionNotFoundException;
use AppBundle\Domain\AndroidSubscription\Query\GetAndroidSubscriptionWithMaxExpirationTimeQuery;
use AppBundle\Domain\Subscription\Command\CreateOrUpdateSubscriptionCommand;
use AppBundle\Domain\Subscription\Command\DeleteSubscriptionCommand;
use AppBundle\Domain\Subscription\Entity\ValueObject\Source;
use AppBundle\Entity\User;
use AppBundle\Module\CommandBus\HasCommandBusTrait;
use AppBundle\Module\CommandBus\HasQueryBusTrait;
use DateTime;

final class UpdateAndroidGeneralSubscriptionHandler
{
    use HasCommandBusTrait;
    use HasQueryBusTrait;

    public function handle(UpdateAndroidGeneralSubscriptionCommand $command): void
    {
        $user = $command->getUser();
        $getSubscriptionQuery = new GetAndroidSubscriptionWithMaxExpirationTimeQuery($user);

        try {
            $subscription = $this->handleQuery($getSubscriptionQuery);
        } catch (AndroidSubscriptionNotFoundException $exception) {
            $this->deleteGeneralSubscription($user);

            return;
        }

        $expirationTime = $command->isTest() ? new DateTime('+ 60 seconds') : (new DateTime())->setTimestamp($subscription->getExpiryTimeMillis() / 1000);

        $this->updateGeneralSubscription($user, $expirationTime);
    }

    private function deleteGeneralSubscription(User $user): void
    {
        $command = new DeleteSubscriptionCommand($user, Source::android());

        $this->handleCommand($command);
    }

    private function updateGeneralSubscription(User $user, DateTime $expirationTime): void
    {
        $command = new CreateOrUpdateSubscriptionCommand($user, Source::android(), $expirationTime);

        $this->handleCommand($command);
    }
}
