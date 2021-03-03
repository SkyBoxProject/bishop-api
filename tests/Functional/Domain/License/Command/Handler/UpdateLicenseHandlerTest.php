<?php

namespace App\Tests\Functional\Domain\License\Command\Handler;

use App\Domain\License\Command\Handler\UpdateLicenseHandler;
use App\Domain\License\Command\UpdateLicenseCommand;
use App\Domain\License\Entity\License;
use App\Domain\License\Entity\ValueObject\LicenseProduct;
use App\Domain\License\Entity\ValueObject\LicenseType;
use App\Domain\License\Repository\LicenseRepository;
use App\Domain\License\TransferObject\LicenseDTO;
use App\Domain\User\Entity\User;
use App\Tests\DataFixtures\ORM\LoadUserWithLicenseFixtures;
use App\Tests\Functional\TestCase;
use DateTime;

final class UpdateLicenseHandlerTest extends TestCase
{
    private LicenseRepository $licenseRepository;
    private UpdateLicenseHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->licenseRepository = $this->getContainer()->get(LicenseRepository::class);
        $this->handler = new UpdateLicenseHandler($this->licenseRepository);
    }

    protected function tearDown(): void
    {
        unset(
            $this->licenseRepository,
            $this->handler
        );

        parent::tearDown();
    }

    public function testCreateLicense(): void
    {
        $referenceRepository = $this->loadFixtures([
            LoadUserWithLicenseFixtures::class,
        ])->getReferenceRepository();

        /** @var User $expectedUser */
        $expectedUser = $referenceRepository->getReference(LoadUserWithLicenseFixtures::REFERENCE_NAME);

        $licenseDTO = new LicenseDTO();
        $licenseDTO
            ->setType(LicenseType::trial())
            ->setProduct(LicenseProduct::feed())
            ->setDescription('test_another')
            ->setOptions(['test_another'])
            ->setNumberOfActivationsLeft(10)
            ->setMaximumNumberOfFeeds(5)
            ->setExpiresAt((new DateTime())->modify('+10 days'));

        $createdLicense = $this->handler->__invoke(new UpdateLicenseCommand($expectedUser->getLicenses()->first(), $licenseDTO));

        self::assertInstanceOf(License::class, $createdLicense);
        self::assertTrue(LicenseProduct::feed()->equals($createdLicense->getProduct()));
        self::assertTrue(LicenseType::trial()->equals($createdLicense->getType()));
        self::assertEquals('test_another', $createdLicense->getDescription());
        self::assertEquals(['test_another'], $createdLicense->getOptions());
        self::assertEquals(10, $createdLicense->getNumberOfActivationsLeft());
        self::assertEquals(5, $createdLicense->getMaximumNumberOfFeeds());
        self::assertEquals(
            $licenseDTO->getExpiresAt()->getTimestamp(),
            $createdLicense->getExpiresAt()->getTimestamp()
        );
    }
}
