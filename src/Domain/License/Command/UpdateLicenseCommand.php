<?php

declare(strict_types=1);

namespace App\Domain\License\Command;

use App\Domain\License\Entity\License;
use App\Domain\License\TransferObject\LicenseDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateLicenseCommand
{
    private License $license;
    private LicenseDTO $licenseDTO;

    public function __construct(License $license, LicenseDTO $licenseDTO)
    {
        $this->licenseDTO = $licenseDTO;
        $this->license = $license;
    }

    public function getLicense(): License
    {
        return $this->license;
    }

    public function getLicenseDTO(): LicenseDTO
    {
        return $this->licenseDTO;
    }

    /**
     * @Assert\IsFalse(message="The license already exists!")
     */
    public function isExistLicenseByProduct(): bool
    {
        $licenseProduct = $this->getLicenseDTO()->getProduct();

        if ($licenseProduct === null) {
            return false;
        }

        if ($licenseProduct->equals($this->license->getProduct())) {
            return false;
        }

        return $this->license->getUser()->getLicenses()->isExistByProduct($licenseProduct);
    }
}
