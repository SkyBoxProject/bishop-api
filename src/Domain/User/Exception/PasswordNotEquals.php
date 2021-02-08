<?php

namespace App\Domain\User\Exception;

use App\Exception\ApiExceptionInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatableMessage;
use function Symfony\Component\Translation\t;

final class PasswordNotEquals extends Exception implements ApiExceptionInterface
{
    private TranslatableMessage $translatableMessage;

    public function __construct()
    {
        $this->translatableMessage = t('User password is wrong!', [], 'error');

        parent::__construct($this->translatableMessage->getMessage(), 400, null);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getTranslatableMessage(): TranslatableMessage
    {
        return $this->translatableMessage;
    }
}
