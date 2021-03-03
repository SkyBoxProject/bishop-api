<?php

namespace App\EventListener;

use App\Exception\ApiExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

final class ApiExceptionListener
{
    private const DEFAULT_ERROR_CODE = 500;

    private TranslatorInterface $translator;
    private LoggerInterface $logger;
    private string $supportEmail;
    private string $environment;
    private string $isForceApiExceptionListener;

    public function __construct(TranslatorInterface $translator, LoggerInterface $logger, string $supportEmail, string $environment, bool $isForceApiExceptionListener)
    {
        $this->translator = $translator;
        $this->logger = $logger;
        $this->supportEmail = $supportEmail;
        $this->environment = $environment;
        $this->isForceApiExceptionListener = $isForceApiExceptionListener;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->isForceApiExceptionListener && !in_array($this->environment, ['prod', 'test'], true)) {
            return;
        }

        $exception = $event->getThrowable();

        $responseData = [
            'code' => self::resolveCode($exception),
            'message' => $this->resolveMessage($exception),
        ];

        $event->allowCustomResponseCode();

        $event->setResponse(new JsonResponse($responseData, self::resolveStatusCode($exception)));
    }

    private static function resolveStatusCode(Throwable $exception): int
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        if ($exception instanceof ApiExceptionInterface) {
            return Response::HTTP_OK;
        }

        if ($exception instanceof HandlerFailedException) {
            return self::resolveStatusCode($exception->getPrevious());
        }

        if ($exception instanceof TooManyLoginAttemptsAuthenticationException) {
            return Response::HTTP_UNAUTHORIZED;
        }

        if (self::isHttpStatusCode($exception->getCode())) {
            return $exception->getCode();
        }

        return self::DEFAULT_ERROR_CODE;
    }

    private static function resolveCode(Throwable $exception): int
    {
        if ($exception instanceof HttpExceptionInterface
            || $exception instanceof ApiExceptionInterface
        ) {
            return $exception->getStatusCode();
        }

        if ($exception instanceof HandlerFailedException) {
            return self::resolveCode($exception->getPrevious());
        }

        if ($exception instanceof TooManyLoginAttemptsAuthenticationException) {
            return Response::HTTP_UNAUTHORIZED . '0';
        }

        if (self::isHttpStatusCode($exception->getCode())) {
            return $exception->getCode();
        }

        return self::DEFAULT_ERROR_CODE;
    }

    private static function isHttpStatusCode(?int $code): bool
    {
        if ($code === null) {
            return false;
        }

        return array_key_exists($code, Response::$statusTexts);
    }

    private function resolveMessage(Throwable $exception): string
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getMessage();
        }

        if ($exception instanceof ApiExceptionInterface) {
            return $exception->getTranslatableMessage()->trans($this->translator);
        }

        if ($exception instanceof HandlerFailedException) {
            return $this->resolveMessage($exception->getPrevious());
        }

        if ($exception instanceof TooManyLoginAttemptsAuthenticationException) {
            return 'Too many login attempts!';
        }

        $this->logError($exception);

        return $this->translator->trans(
            'An Internal Error Occurred. Please try again or contact email.',
            ['%support_email%' => $this->supportEmail],
            'error'
        );
    }

    private function logError(Throwable $exception): void
    {
        $message = sprintf(
            'Error from request. Message: %s, File(%s): %s, Trace: %s',
            $exception->getMessage(),
            $exception->getLine(),
            $exception->getFile(),
            $exception->getTraceAsString()
        );

        $this->logger->error($message);
    }
}
