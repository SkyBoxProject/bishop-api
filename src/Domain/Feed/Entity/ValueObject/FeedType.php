<?php

namespace App\Domain\Feed\Entity\ValueObject;

use MyCLabs\Enum\Enum;

final class FeedType extends Enum
{
    private const MICRO = 'micro';
    private const BASIC = 'basic';

    public static function micro(): self
    {
        return new self(self::MICRO);
    }

    public static function basic(): self
    {
        return new self(self::BASIC);
    }
}
