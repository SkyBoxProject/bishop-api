<?php

namespace AppBundle\Domain\AndroidSubscription\Command\Handler;

use AppBundle\Domain\AndroidSubscription\Command\UpdateAndroidSubscriptionCommand;

final class UpdateAndroidSubscriptionHandler extends CommonAndroidSubscriptionHandler
{
    public function handle(UpdateAndroidSubscriptionCommand $command): void
    {
        $androidSubscription = $command->getAndroidSubscription();

        $androidSubscription->setUser($command->getUser());

        $this->update($androidSubscription, $command->getPayload(), $command);
    }
}
