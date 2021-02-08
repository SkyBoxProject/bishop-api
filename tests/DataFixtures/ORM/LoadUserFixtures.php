<?php

namespace App\Tests\DataFixtures\ORM;

use App\Domain\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class LoadUserFixtures extends Fixture
{
    public const REFERENCE_NAME = 'test_user';

    public const PLAIN_PASSWORD = 'test_password';

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $user = new User();
        $user
            ->setEmail($faker->email)
            ->setPassword($this->passwordEncoder->encodePassword(
                $user,
                self::PLAIN_PASSWORD
            ));

        $manager->persist($user);
        $manager->flush();

        $this->addReference(self::REFERENCE_NAME, $user);
    }
}
