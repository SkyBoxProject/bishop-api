<?php

namespace App\Domain\License\Entity\ValueObject;

use MyCLabs\Enum\Enum;

final class LicenseProduct extends Enum
{
    private const FEED = 'feed';

    public static function feed(): self
    {
        return new self(self::FEED);
    }
}
