<?php

namespace AppBundle\Domain\AndroidSubscription\Command;

use AppBundle\Domain\AndroidSubscription\TransferObject\RequestAndroidSubscriptionVerification;

final class VerifyAndroidSubscriptionCommand
{
    private $requestAndroidSubscriptionVerification;

    public function __construct(RequestAndroidSubscriptionVerification $requestAndroidSubscriptionVerification)
    {
        $this->requestAndroidSubscriptionVerification = $requestAndroidSubscriptionVerification;
    }

    public function getRequestAndroidSubscriptionVerification(): RequestAndroidSubscriptionVerification
    {
        return $this->requestAndroidSubscriptionVerification;
    }
}
