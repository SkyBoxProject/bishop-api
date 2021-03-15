<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\Exception\PasswordNotEquals;
use App\Domain\User\Exception\UserNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\UuidV4;

/**
 * @final
 */
class UserRepository implements PasswordUpgraderInterface
{
    private EntityManagerInterface $entityRepository;

    private UserPasswordEncoderInterface $passwordEncoder;

    private ObjectRepository $userRepository;

    public function __construct(EntityManagerInterface $entityRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityRepository = $entityRepository;
        $this->userRepository = $entityRepository->getRepository(User::class);
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getByEmailAndPassword(string $email, string $password): User
    {
        $user = $this->getByEmail($email);

        $isPasswordValid = $this->passwordEncoder->isPasswordValid($user, $password);

        if (!$isPasswordValid) {
            throw new PasswordNotEquals();
        }

        return $user;
    }

    public function getByEmail(string $email): User
    {
        try {
            /** @var User $user */
            $user = $this->userRepository->createQueryBuilder('u')
                ->andWhere('u.email = :email')
                ->setParameter('email', $email)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $exception) {
            throw new UserNotFound();
        }

        if ($user === null) {
            throw new UserNotFound();
        }

        return $user;
    }

    public function getById(UuidV4 $id): User
    {
        try {
            /** @var User $user */
            $user = $this->userRepository->createQueryBuilder('u')
                ->andWhere('u.id = :id')
                ->setParameter('id', $id->toBinary())
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $exception) {
            throw new UserNotFound();
        }

        if ($user === null) {
            throw new UserNotFound();
        }

        return $user;
    }

    public function save(User $user): void
    {
        $this->entityRepository->persist($user);
        $this->entityRepository->flush();
    }

    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->save($user);
    }
}
