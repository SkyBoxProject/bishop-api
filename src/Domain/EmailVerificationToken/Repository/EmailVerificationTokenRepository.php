<?php

namespace App\Domain\EmailVerificationToken\Repository;

use App\Domain\EmailVerificationToken\Entity\EmailVerificationToken;
use App\Domain\EmailVerificationToken\Exception\EmailVerificationTokenNotFound;
use App\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ObjectRepository;

/**
 * @final
 */
class EmailVerificationTokenRepository
{
    private EntityManagerInterface $entityRepository;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $entityRepository)
    {
        $this->entityRepository = $entityRepository;
        $this->repository = $entityRepository->getRepository(EmailVerificationToken::class);
    }

    public function getByToken(string $token): EmailVerificationToken
    {
        try {
            /** @var EmailVerificationToken $emailVerificationToken */
            $emailVerificationToken = $this->repository->createQueryBuilder('evt')
                ->andWhere('evt.token = :token')
                ->setParameter('token', $token)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $exception) {
            throw new EmailVerificationTokenNotFound($token);
        }

        if ($emailVerificationToken === null) {
            throw new EmailVerificationTokenNotFound($token);
        }

        return $emailVerificationToken;
    }

    public function save(EmailVerificationToken $user): void
    {
        $this->entityRepository->persist($user);
        $this->entityRepository->flush();
    }
}
