<?php

namespace AppBundle\Domain\AndroidSubscription\Command\Handler;

use AppBundle\Domain\AndroidSubscription\Command\CreateAndroidSubscriptionCommand;
use AppBundle\Domain\AndroidSubscription\Entity\AndroidSubscription;
use AppBundle\Module\CommandBus\HasCommandBusTrait;

final class CreateAndroidSubscriptionHandler extends CommonAndroidSubscriptionHandler
{
    use HasCommandBusTrait;

    public function handle(CreateAndroidSubscriptionCommand $command): void
    {
        $androidSubscription = new AndroidSubscription($command->getUser());

        $this->update($androidSubscription, $command->getPayload(), $command);
    }
}
