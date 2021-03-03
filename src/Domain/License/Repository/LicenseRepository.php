<?php

namespace App\Domain\License\Repository;

use App\Domain\License\Entity\License;
use App\Domain\License\Exception\LicenseNotFoundException;
use App\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Uid\UuidV4;

/**
 * @final
 */
class LicenseRepository
{
    private EntityManagerInterface $entityRepository;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $entityRepository)
    {
        $this->entityRepository = $entityRepository;
        $this->repository = $entityRepository->getRepository(License::class);
    }

    public function getByUuid(UuidV4 $uuid): License
    {
        try {
            /** @var License $license */
            $license = $this->repository->createQueryBuilder('license')
                ->andWhere('license.uuid = :uuid')
                ->setParameter('uuid', $uuid->toBinary())
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $exception) {
            throw LicenseNotFoundException::createByUuid($uuid);
        }

        if ($license === null) {
            throw LicenseNotFoundException::createByUuid($uuid);
        }

        return $license;
    }

    /**
     * @return License[]
     */
    public function getByUser(User $user): array
    {
        return $this->repository->createQueryBuilder('license')
            ->andWhere('license.user = :user')
            ->setParameter('user', $user->getId()->toBinary())
            ->getQuery()
            ->getResult();
    }

    public function save(License $license): void
    {
        $this->entityRepository->persist($license);
        $this->entityRepository->flush();
    }
}
