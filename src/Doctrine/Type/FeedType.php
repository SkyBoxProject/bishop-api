<?php

namespace App\Doctrine\Type;

use App\Domain\Feed\Entity\ValueObject\FeedType as FeedTypeEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class FeedType extends AbstractEnumType
{
    private const TYPE_NAME = 'feed_type';

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
        return FeedTypeEnum::class;
    }
}
