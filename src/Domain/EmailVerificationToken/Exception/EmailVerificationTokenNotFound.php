<?php

namespace App\Domain\EmailVerificationToken\Exception;

use App\Exception\ApiExceptionInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\Translation\t;
use Symfony\Component\Translation\TranslatableMessage;

final class EmailVerificationTokenNotFound extends Exception implements ApiExceptionInterface
{
    private TranslatableMessage $translatableMessage;

    public function __construct(string $token)
    {
        $this->translatableMessage = t('Email verification token not found.', ['%token%' => $token], 'error');

        parent::__construct($this->translatableMessage->getMessage(), 404, null);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getTranslatableMessage(): TranslatableMessage
    {
        return $this->translatableMessage;
    }
}
