<?php

namespace App\Domain\License\Exception;

use App\Domain\License\Entity\ValueObject\LicenseProduct;
use App\Exception\ApiExceptionInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\UuidV4;
use function Symfony\Component\Translation\t;
use Symfony\Component\Translation\TranslatableMessage;

final class LicenseNotFoundException extends Exception implements ApiExceptionInterface
{
    private TranslatableMessage $translatableMessage;

    public function __construct(TranslatableMessage $translatableMessage)
    {
        $this->translatableMessage = $translatableMessage;

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

    public static function createByProduct(LicenseProduct $licenseProduct): LicenseNotFoundException
    {
        return new self(t('License not found by the product.', ['%product%' => $licenseProduct->getValue()], 'error'));
    }

    public static function createByUuid(UuidV4 $uuidV4): LicenseNotFoundException
    {
        return new self(t('License not found.', ['%uuid%' => $uuidV4->toBinary()], 'error'));
    }
}
