<?php

namespace App\EventListener;

use App\Exception\ApiExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

final class ApiExceptionListener
{
    private const DEFAULT_ERROR_CODE = 500;

    private TranslatorInterface $translator;
    private LoggerInterface $logger;
    private string $supportEmail;
    private string $environment;

    public function __construct(TranslatorInterface $translator, LoggerInterface $logger, string $supportEmail, string $environment)
    {
        $this->translator = $translator;
        $this->logger = $logger;
        $this->supportEmail = $supportEmail;
        $this->environment = $environment;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->environment !== 'prod') {
            return;
        }

        $exception = $event->getThrowable();

        $responseData = [
            'code' => self::resolveStatusCode($exception),
            'message' => $this->resolveMessage($exception),
        ];

        $event->allowCustomResponseCode();

        $event->setResponse(new JsonResponse($responseData));
    }

    private static function resolveStatusCode(Throwable $exception): int
    {
        if (self::isHttpException($exception)) {
            return $exception->getStatusCode();
        }

        if (self::isHttpStatusCode($exception->getCode())) {
            return $exception->getCode();
        }

        return self::DEFAULT_ERROR_CODE;
    }

    private static function isHttpStatusCode(?int $code): bool
    {
        if (null === $code) {
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

        $this->logError($exception);

        return $this->translator->trans('An Internal Error Occurred. Please try again or contact email.', ['%support_email%' => $this->supportEmail], 'error');
    }

    private static function isHttpException(Throwable $exception): bool
    {
        return $exception instanceof HttpExceptionInterface
            || $exception instanceof ApiExceptionInterface;
    }

    private function logError(Throwable $exception): void
    {
        $message = sprintf('Error from request. Message: %s, File(%s): %s, Trace: %s',
            $exception->getMessage(),
            $exception->getLine(),
            $exception->getFile(),
            $exception->getTraceAsString()
        );

        $this->logger->error($message);
    }
}