<?php

namespace App\Domain\Feed\Exception;

use App\Exception\ApiExceptionInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\Translation\t;
use Symfony\Component\Translation\TranslatableMessage;

final class FeedTypeNotFound extends Exception implements ApiExceptionInterface
{
    private TranslatableMessage $translatableMessage;

    public function __construct(string $url)
    {
        $this->translatableMessage = t('Feed type not found.', ['%url%' => $url], 'error');

        parent::__construct($this->translatableMessage->getMessage(), Response::HTTP_BAD_REQUEST, null);
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
