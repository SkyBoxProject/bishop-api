<?php

namespace App\Module\RateLimiter;

use Symfony\Component\HttpFoundation\RateLimiter\AbstractRequestRateLimiter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Core\Security;

final class ApiLoginRequestRateLimiter extends AbstractRequestRateLimiter
{
    private RateLimiterFactory $globalFactory;
    private RateLimiterFactory $localFactory;

    public function __construct(RateLimiterFactory $globalFactory, RateLimiterFactory $localFactory)
    {
        $this->globalFactory = $globalFactory;
        $this->localFactory = $localFactory;
    }

    protected function getLimiters(Request $request): array
    {
        return [
            $this->globalFactory->create($request->getClientIp()),
            $this->localFactory->create($request->attributes->get(Security::LAST_USERNAME).$request->getClientIp()),
        ];
    }
}
