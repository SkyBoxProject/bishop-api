<?php

namespace App\Tests\Functional\Domain\License\Command;

use App\Domain\License\Command\CreateLicenseCommand;
use App\Domain\License\Entity\License;
use App\Domain\License\Entity\ValueObject\LicenseProduct;
use App\Domain\License\Entity\ValueObject\LicenseType;
use App\Domain\License\TransferObject\LicenseDTO;
use App\Domain\User\Entity\User;
use App\Tests\Functional\ValidationTestCase;
use DateTime;
use Symfony\Component\Uid\UuidV4;

final class CreateLicenseCommandValidationTest extends ValidationTestCase
{
    public function testWithValidData(): void
    {
        $user = new User(UuidV4::v4(), 'test@email.com');
        $licenseDTO = new LicenseDTO();
        $licenseDTO
            ->setType(LicenseType::trial())
            ->setProduct(LicenseProduct::feed())
            ->setDescription('test')
            ->setOptions(['test'])
            ->setNumberOfActivationsLeft(50)
            ->setMaximumNumberOfFeeds(49)
            ->setExpiresAt(new DateTime());

        $command = new CreateLicenseCommand($user, $licenseDTO);

        $errors = $this->getValidator()->validate($command);

        self::assertEmpty($errors);
    }

    public function testWithEmptyProduct(): void
    {
        $user = new User(UuidV4::v4(), 'test@email.com');
        $licenseDTO = new LicenseDTO();
        $licenseDTO
            ->setType(LicenseType::trial())
            ->setDescription('test')
            ->setOptions(['test'])
            ->setNumberOfActivationsLeft(50)
            ->setMaximumNumberOfFeeds(49)
            ->setExpiresAt(new DateTime());

        $command = new CreateLicenseCommand($user, $licenseDTO);

        $errors = $this->getValidator()->validate($command);

        self::assertCount(1, $errors);
        $this->assertOnlyFieldsAreInvalid($command, ['existProductField'], null, 'Product field is required!');
    }

    public function testWithEmptyType(): void
    {
        $user = new User(UuidV4::v4(), 'test@email.com');
        $licenseDTO = new LicenseDTO();
        $licenseDTO
            ->setProduct(LicenseProduct::feed())
            ->setDescription('test')
            ->setOptions(['test'])
            ->setNumberOfActivationsLeft(50)
            ->setMaximumNumberOfFeeds(49)
            ->setExpiresAt(new DateTime());

        $command = new CreateLicenseCommand($user, $licenseDTO);

        $errors = $this->getValidator()->validate($command);

        self::assertCount(1, $errors);
        $this->assertOnlyFieldsAreInvalid($command, ['existTypeField'], null, 'Type field is required!');
    }

    public function testWithExistLicenseProduct(): void
    {
        $user = new User(UuidV4::v4(), 'test@email.com');
        new License(UuidV4::v4(), LicenseProduct::feed(), LicenseType::trial(), $user);

        $licenseDTO = new LicenseDTO();
        $licenseDTO
            ->setType(LicenseType::trial())
            ->setProduct(LicenseProduct::feed())
            ->setDescription('test')
            ->setOptions(['test'])
            ->setNumberOfActivationsLeft(50)
            ->setMaximumNumberOfFeeds(49)
            ->setExpiresAt(new DateTime());

        $command = new CreateLicenseCommand($user, $licenseDTO);

        $errors = $this->getValidator()->validate($command);

        self::assertCount(1, $errors);
        $this->assertOnlyFieldsAreInvalid($command, ['existLicenseByProduct'], null, 'The license already exists!');
    }
}
