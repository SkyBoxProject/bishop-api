<?php

namespace App\Tests\Unit\Domain\License\Collection;

use App\Domain\License\Collection\LicenseCollection;
use App\Domain\License\Entity\License;
use App\Domain\License\Entity\ValueObject\LicenseProduct;
use App\Domain\License\Entity\ValueObject\LicenseType;
use App\Domain\User\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

final class LicenseCollectionTest extends TestCase
{
    public function testGetByProduct(): void
    {
        $expectedLicense = $this->createLicense();

        $licenses = new LicenseCollection([$expectedLicense]);

        $actualLicense = $licenses->getByProduct(LicenseProduct::feed());

        self::assertInstanceOf(License::class, $actualLicense);
        self::assertTrue($expectedLicense->getUuid()->equals($actualLicense->getUuid()));
    }

    public function testIsExistByProduct(): void
    {
        $expectedLicense = $this->createLicense();

        $licenses = new LicenseCollection([$expectedLicense]);

        self::assertTrue($licenses->isExistByProduct(LicenseProduct::feed()));
    }

    private function createLicense(): License
    {
        $user = new User(UuidV4::v4(), 'test@email.com');

        return new License(UuidV4::v4(), LicenseProduct::feed(), LicenseType::trial(), $user);
    }
}
