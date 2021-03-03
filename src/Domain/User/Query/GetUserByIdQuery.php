<?php

namespace App\Domain\User\Query;

use Symfony\Component\Uid\UuidV4;

final class GetUserByIdQuery
{
    private UuidV4 $uuid;

    public function __construct(UuidV4 $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getId(): UuidV4
    {
        return $this->uuid;
    }
}
