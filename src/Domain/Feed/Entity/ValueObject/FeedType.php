<?php

namespace App\Domain\Feed\Entity\ValueObject;

use MyCLabs\Enum\Enum;

final class FeedType extends Enum
{
    private const MINI = 'mini';
    private const FULL = 'full';

    public static function mini(): self
    {
        return new self(self::MINI);
    }

    public static function full(): self
    {
        return new self(self::FULL);
    }
}
