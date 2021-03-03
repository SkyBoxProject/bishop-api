<?php

namespace App\Tests\Functional\Domain\License\Command\Handler;

use App\Domain\License\Command\CreateLicenseCommand;
use App\Domain\License\Command\Handler\CreateLicenseHandler;
use App\Domain\License\Entity\License;
use App\Domain\License\Entity\ValueObject\LicenseProduct;
use App\Domain\License\Entity\ValueObject\LicenseType;
use App\Domain\License\Repository\LicenseRepository;
use App\Domain\License\TransferObject\LicenseDTO;
use App\Domain\User\Entity\User;
use App\Tests\DataFixtures\ORM\LoadUserFixtures;
use App\Tests\Functional\TestCase;
use DateTime;

final class CreateLicenseHandlerTest extends TestCase
{
    private LicenseRepository $licenseRepository;
    private CreateLicenseHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->licenseRepository = $this->getContainer()->get(LicenseRepository::class);
        $this->handler = new CreateLicenseHandler($this->licenseRepository);
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
            LoadUserFixtures::class,
        ])->getReferenceRepository();

        /** @var User $expectedUser */
        $expectedUser = $referenceRepository->getReference(LoadUserFixtures::REFERENCE_NAME);

        $licenseDTO = new LicenseDTO();
        $licenseDTO
            ->setType(LicenseType::trial())
            ->setProduct(LicenseProduct::feed())
            ->setDescription('test')
            ->setOptions(['test'])
            ->setNumberOfActivationsLeft(50)
            ->setMaximumNumberOfFeeds(49)
            ->setExpiresAt(new DateTime());

        $createdLicense = $this->handler->__invoke(new CreateLicenseCommand($expectedUser, $licenseDTO));

        self::assertInstanceOf(License::class, $createdLicense);
        self::assertTrue(LicenseProduct::feed()->equals($createdLicense->getProduct()));
        self::assertTrue(LicenseType::trial()->equals($createdLicense->getType()));
        self::assertEquals('test', $createdLicense->getDescription());
        self::assertEquals(['test'], $createdLicense->getOptions());
        self::assertEquals(50, $createdLicense->getNumberOfActivationsLeft());
        self::assertEquals(49, $createdLicense->getMaximumNumberOfFeeds());
        self::assertEquals(
            $licenseDTO->getExpiresAt()->getTimestamp(),
            $createdLicense->getExpiresAt()->getTimestamp()
        );
    }
}
