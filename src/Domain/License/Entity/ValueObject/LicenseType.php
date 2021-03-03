<?php

namespace App\Domain\License\Entity\ValueObject;

use MyCLabs\Enum\Enum;

final class LicenseType extends Enum
{
    private const TRIAL = 'trial';
    private const EXPIRES_PERIOD = 'expires_period';
    private const NUMBER_OF_ACTIVATIONS = 'number_of_activations';
    private const UNRESTRICTED = 'unrestricted';

    public static function trial(): self
    {
        return new self(self::TRIAL);
    }

    public static function expiresPeriod(): self
    {
        return new self(self::EXPIRES_PERIOD);
    }

    public static function numberOfActivations(): self
    {
        return new self(self::NUMBER_OF_ACTIVATIONS);
    }

    public static function unrestricted(): self
    {
        return new self(self::UNRESTRICTED);
    }
}
