<?php

namespace AppBundle\Domain\AndroidSubscription\Repository;

use AppBundle\Domain\AndroidSubscription\Entity\AndroidSubscription;
use AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory\AndroidSubscriptionCancelReasonIsNull;
use AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory\AndroidSubscriptionExpiresDateLess;
use AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory\AndroidSubscriptionNotificationTypeNotEquals;
use AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory\AndroidSubscriptionOrderIdEquals;
use AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory\AndroidSubscriptionPurchaseDateLess;
use AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory\AndroidSubscriptionUserIdEquals;
use AppBundle\Domain\AndroidSubscription\Repository\ExpressionFactory\OrderByExpiryTimeMillisDesc;
use AppBundle\Entity\User;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * @final
 */
class AndroidSubscriptionRepository
{
    private const TABLE_ALIAS = 'androidSubscription';

    private $entityManager;

    /** @var EntityRepository */
    private $doctrineAndroidSubscriptionRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->doctrineAndroidSubscriptionRepository = $entityManager->getRepository(AndroidSubscription::class);
    }

    public function findByOrderId(string $orderId): ?AndroidSubscription
    {
        $queryBuilder = $this->doctrineAndroidSubscriptionRepository->createQueryBuilder(self::TABLE_ALIAS);

        return $queryBuilder
            ->where(AndroidSubscriptionOrderIdEquals::create(self::TABLE_ALIAS, $orderId))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return AndroidSubscription[]
     */
    public function findByUser(User $user): array
    {
        $queryBuilder = $this->doctrineAndroidSubscriptionRepository->createQueryBuilder(self::TABLE_ALIAS);

        return $queryBuilder
            ->where(AndroidSubscriptionUserIdEquals::create(self::TABLE_ALIAS, $user))
            ->getQuery()
            ->getResult();
    }

    public function findPreviousExpiredAndroidSubscriptionByPurchaseDate(DateTimeInterface $purchaseDate, User $user): ?AndroidSubscription
    {
        $queryBuilder = $this->doctrineAndroidSubscriptionRepository->createQueryBuilder(self::TABLE_ALIAS);

        return $queryBuilder
            ->where(AndroidSubscriptionPurchaseDateLess::create(self::TABLE_ALIAS, $purchaseDate))
            ->andWhere(AndroidSubscriptionExpiresDateLess::create(self::TABLE_ALIAS, new DateTime('now', new DateTimeZone('UTC'))))
            ->andWhere(AndroidSubscriptionCancelReasonIsNull::create(self::TABLE_ALIAS))
            ->andWhere(AndroidSubscriptionUserIdEquals::create(self::TABLE_ALIAS, $user))
            ->orderBy(sprintf('%s.startTimeUTC', self::TABLE_ALIAS), 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAndroidSubscriptionWithMaxExpirationTime(User $user): ?AndroidSubscription
    {
        return $this->doctrineAndroidSubscriptionRepository->createQueryBuilder(self::TABLE_ALIAS)
            ->andWhere(AndroidSubscriptionUserIdEquals::create(self::TABLE_ALIAS, $user))
            ->andWhere(AndroidSubscriptionNotificationTypeNotEquals::create(self::TABLE_ALIAS, AndroidSubscription::SUBSCRIPTION_REVOKED_NOTIFICATION_TYPE))
            ->orderBy(OrderByExpiryTimeMillisDesc::create(self::TABLE_ALIAS))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(AndroidSubscription $androidSubscription): void
    {
        $this->entityManager->persist($androidSubscription);
        $this->entityManager->flush();
    }
}
