<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\Exception\UserNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @final
 */
class UserRepository
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

        $isPasswordValid = $this->passwordEncoder->isPasswordValid(
            $user,
            $password
        );

        if (!$isPasswordValid) {
            throw new UserNotFound();
        }

        return $user;
    }

    public function save(User $user): void
    {
        $this->entityRepository->persist($user);
        $this->entityRepository->flush();
    }

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
