<?php

namespace App\Module\RateLimiter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RateLimiter\RequestRateLimiterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Component\Security\Core\Security;

final class LoginThrottlingListener implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private RequestRateLimiterInterface $limiter;

    public function __construct(RequestStack $requestStack, RequestRateLimiterInterface $limiter)
    {
        $this->requestStack = $requestStack;
        $this->limiter = $limiter;
    }

    public function checkPassport(RequestEvent $event): void
    {
        if ($event->getRequest()->getRequestUri() !== '/login') {
            return;
        }

        $request = $this->requestStack->getMasterRequest();
        $request->attributes->set(Security::LAST_USERNAME, $event->getRequest()->get('email', 'anon'));

        $limit = $this->limiter->consume($request);

        if (!$limit->isAccepted()) {
            throw new TooManyLoginAttemptsAuthenticationException(ceil(($limit->getRetryAfter()->getTimestamp() - time()) / 60));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['checkPassport', 2080],
        ];
    }
}
