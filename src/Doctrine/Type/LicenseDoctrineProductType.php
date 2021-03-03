<?php

namespace App\Doctrine\Type;

use App\Domain\License\Entity\ValueObject\LicenseProduct;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class LicenseDoctrineProductType extends AbstractEnumType
{
    private const TYPE_NAME = 'license_product';

    /**
     * @inheritDoc
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    /**
     * @inheritDoc
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    protected function getEnumClassName(): string
    {
        return LicenseProduct::class;
    }
}
