<?php

namespace App\Domain\Feed\Exception;

use App\Exception\ApiExceptionInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\UuidV4;
use function Symfony\Component\Translation\t;
use Symfony\Component\Translation\TranslatableMessage;

final class FeedNotFound extends Exception implements ApiExceptionInterface
{
    private TranslatableMessage $translatableMessage;

    public function __construct(UuidV4 $uuid)
    {
        $this->translatableMessage = t('Feed not found.', ['%uuid%' => $uuid], 'error');

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
