<?php

namespace App\Exception;

use Symfony\Component\Translation\TranslatableMessage;

interface ApiExceptionInterface
{
    public function getStatusCode(): int;

    public function getTranslatableMessage(): TranslatableMessage;
}
