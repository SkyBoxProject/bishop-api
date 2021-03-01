<?php

namespace App\Domain\Feed\Repository;

use App\Domain\Feed\Entity\Feed;
use App\Domain\Feed\Exception\FeedNotFound;
use App\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Uid\UuidV4;

/**
 * @final
 */
class FeedRepository
{
    private EntityManagerInterface $entityRepository;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $entityRepository)
    {
        $this->entityRepository = $entityRepository;
        $this->repository = $entityRepository->getRepository(Feed::class);
    }

    public function getByUuid(UuidV4 $uuid): Feed
    {
        try {
            /** @var Feed $feed */
            $feed = $this->repository->createQueryBuilder('feed')
                ->andWhere('feed.uuid = :uuid')
                ->setParameter('uuid', $uuid->toBinary())
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $exception) {
            throw new FeedNotFound($uuid);
        }

        if ($feed === null) {
            throw new FeedNotFound($uuid);
        }

        return $feed;
    }

    /**
     * @return Feed[]
     */
    public function getByUser(User $user): array
    {
        return $this->repository->createQueryBuilder('feed')
            ->andWhere('feed.user = :user')
            ->setParameter('user', $user->getId()->toBinary())
            ->getQuery()
            ->getResult();
    }

    public function save(Feed $user): void
    {
        $this->entityRepository->persist($user);
        $this->entityRepository->flush();
    }
}
