<?php

namespace App\Tests\DataFixtures\ORM;

use App\Domain\License\Entity\License;
use App\Domain\License\Entity\ValueObject\LicenseProduct;
use App\Domain\License\Entity\ValueObject\LicenseType;
use App\Domain\License\TransferObject\LicenseDTO;
use App\Domain\User\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

final class LoadUserWithLicenseFixtures extends Fixture
{
    public const REFERENCE_NAME = 'test_user_with_license';

    public const PLAIN_PASSWORD = 'test_password';

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $user = new User(Uuid::v4(), $faker->email);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            self::PLAIN_PASSWORD
        ));

        $license = new License(UuidV4::v4(), LicenseProduct::feed(), LicenseType::trial(), $user);

        $licenseDTO = new LicenseDTO();
        $licenseDTO
            ->setDescription('test')
            ->setOptions(['test'])
            ->setNumberOfActivationsLeft(50)
            ->setMaximumNumberOfFeeds(49)
            ->setExpiresAt(new DateTime());

        $license->updateFromDTO($licenseDTO);

        $manager->persist($user);
        $manager->persist($license);
        $manager->flush();

        $this->addReference(self::REFERENCE_NAME, $user);
    }
}
