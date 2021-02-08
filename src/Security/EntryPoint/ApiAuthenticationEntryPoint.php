<?php

namespace App\Security\EntryPoint;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ApiAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private TranslatorInterface $translator;
    private LoggerInterface $logger;

    public function __construct(TranslatorInterface $translator, LoggerInterface $logger)
    {
        $this->translator = $translator;
        $this->logger = $logger;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        if ($authException) {
            $this->logError($authException);
        }

        return new JsonResponse([
            'code' => Response::HTTP_UNAUTHORIZED,
            'message' => $this->translator->trans('Authorization required!', [], 'error'),
        ], Response::HTTP_UNAUTHORIZED);
    }

    private function logError(AuthenticationException $exception): void
    {
        $message = sprintf(
            'Auth error from request. Message: %s, File(%s): %s, Trace: %s',
            $exception->getMessage(),
            $exception->getLine(),
            $exception->getFile(),
            $exception->getTraceAsString()
        );

        $this->logger->error($message);
    }
}
