<?php

namespace App\Domain\Feed\Query;

use Symfony\Component\Uid\UuidV4;

final class GetFeedByUuidQuery
{
    private UuidV4 $uuid;

    public function __construct(UuidV4 $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid(): UuidV4
    {
        return $this->uuid;
    }
}
