<?php

declare(strict_types=1);

namespace App\Domain\License\Command;

use App\Domain\License\Entity\ValueObject\LicenseProduct;
use App\Domain\License\Entity\ValueObject\LicenseType;
use App\Domain\License\TransferObject\LicenseDTO;
use App\Domain\User\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateLicenseCommand
{
    private User $user;
    private LicenseDTO $licenseDTO;

    public function __construct(User $user, LicenseDTO $feedDTO)
    {
        $this->licenseDTO = $feedDTO;
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getLicenseDTO(): LicenseDTO
    {
        return $this->licenseDTO;
    }

    /**
     * @Assert\IsTrue(message="Type field is required!")
     */
    public function isExistTypeField(): bool
    {
        return $this->getLicenseDTO()->getType() instanceof LicenseType;
    }

    /**
     * @Assert\IsTrue(message="Product field is required!")
     */
    public function isExistProductField(): bool
    {
        return $this->getLicenseDTO()->getProduct() instanceof LicenseProduct;
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

        return $this->user->getLicenses()->isExistByProduct($licenseProduct);
    }
}
