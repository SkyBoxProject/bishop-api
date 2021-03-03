<?php

namespace App\Domain\License\Collection;

use App\Domain\License\Entity\License;
use App\Domain\License\Entity\ValueObject\LicenseProduct;
use App\Domain\License\Exception\LicenseNotFoundException;
use App\Module\Collection\ObjectCollection;
use Doctrine\Common\Collections\Criteria;

final class LicenseCollection extends ObjectCollection
{
    protected static function getItemType(): string
    {
        return License::class;
    }

    public function first(): ?License
    {
        return $this->getFirstItem();
    }

    public function last(): ?License
    {
        return $this->getLastItem();
    }

    /**
     * @return License[]
     */
    public function toArray(): array
    {
        return $this->getItems();
    }

    /**
     * @throws LicenseNotFoundException
     */
    public function getByProduct(LicenseProduct $licenseProduct): ?License
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->contains('product', $licenseProduct->getValue()));

        $license = $this->matching($criteria)->first();

        if ($license === null) {
            throw LicenseNotFoundException::createByProduct($licenseProduct);
        }

        return $license;
    }

    public function isExistByProduct(LicenseProduct $licenseProduct): bool
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->contains('product', $licenseProduct->getValue()));

        $license = $this->matching($criteria)->first();

        return $license instanceof License;
    }
}
