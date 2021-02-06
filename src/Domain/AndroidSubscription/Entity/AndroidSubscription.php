<?php

namespace AppBundle\Domain\AndroidSubscription\Entity;

use AppBundle\Entity\User;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\Table(name="android_subscription")
 * @ORM\Entity()
 *
 * @final
 */
class AndroidSubscription
{
    public const UNKNOWN_NOTIFICATION_TYPE = 0;
    public const SUBSCRIPTION_RECOVERED_NOTIFICATION_TYPE = 1;
    public const SUBSCRIPTION_RENEWED_NOTIFICATION_TYPE = 2;
    public const SUBSCRIPTION_CANCELED_NOTIFICATION_TYPE = 3;
    public const SUBSCRIPTION_PURCHASED_NOTIFICATION_TYPE = 4;
    public const SUBSCRIPTION_ON_HOLD_NOTIFICATION_TYPE = 5;
    public const SUBSCRIPTION_IN_GRACE_PERIOD_NOTIFICATION_TYPE = 6;
    public const SUBSCRIPTION_RESTARTED_NOTIFICATION_TYPE = 7;
    public const SUBSCRIPTION_PRICE_CHANGE_CONFIRMED_NOTIFICATION_TYPE = 8;
    public const SUBSCRIPTION_DEFERRED_NOTIFICATION_TYPE = 9;
    public const SUBSCRIPTION_PAUSED_NOTIFICATION_TYPE = 10;
    public const SUBSCRIPTION_PAUSE_SCHEDULE_CHANGED_NOTIFICATION_TYPE = 11;
    public const SUBSCRIPTION_REVOKED_NOTIFICATION_TYPE = 12;
    public const SUBSCRIPTION_EXPIRED_NOTIFICATION_TYPE = 13;

    public const NOTIFICATION_TYPES = [
        self::UNKNOWN_NOTIFICATION_TYPE => 'UNKNOWN',
        self::SUBSCRIPTION_RECOVERED_NOTIFICATION_TYPE => 'SUBSCRIPTION_RECOVERED',
        self::SUBSCRIPTION_RENEWED_NOTIFICATION_TYPE => 'SUBSCRIPTION_RENEWED',
        self::SUBSCRIPTION_CANCELED_NOTIFICATION_TYPE => 'SUBSCRIPTION_CANCELED',
        self::SUBSCRIPTION_PURCHASED_NOTIFICATION_TYPE => 'SUBSCRIPTION_PURCHASED',
        self::SUBSCRIPTION_ON_HOLD_NOTIFICATION_TYPE => 'SUBSCRIPTION_ON_HOLD',
        self::SUBSCRIPTION_IN_GRACE_PERIOD_NOTIFICATION_TYPE => 'SUBSCRIPTION_IN_GRACE_PERIOD',
        self::SUBSCRIPTION_RESTARTED_NOTIFICATION_TYPE => 'SUBSCRIPTION_RESTARTED',
        self::SUBSCRIPTION_PRICE_CHANGE_CONFIRMED_NOTIFICATION_TYPE => 'SUBSCRIPTION_PRICE_CHANGE_CONFIRMED',
        self::SUBSCRIPTION_DEFERRED_NOTIFICATION_TYPE => 'SUBSCRIPTION_DEFERRED',
        self::SUBSCRIPTION_PAUSED_NOTIFICATION_TYPE => 'SUBSCRIPTION_PAUSED',
        self::SUBSCRIPTION_PAUSE_SCHEDULE_CHANGED_NOTIFICATION_TYPE => 'SUBSCRIPTION_PAUSE_SCHEDULE_CHANGED',
        self::SUBSCRIPTION_REVOKED_NOTIFICATION_TYPE => 'SUBSCRIPTION_REVOKED',
        self::SUBSCRIPTION_EXPIRED_NOTIFICATION_TYPE => 'SUBSCRIPTION_EXPIRED',
    ];

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="acknowledgementState", type="integer", nullable=true)
     */
    private $acknowledgementState;

    /**
     * @var float
     *
     * @ORM\Column(name="at_purchase_lifetime", type="float", options={"default" : 0})
     */
    private $atPurchaseLifetimeInMonths = 0;

    /**
     * @ORM\Column(name="autoRenewing", type="boolean", nullable=true)
     */
    private $autoRenewing;

    /**
     * @ORM\Column(name="autoResumeTimeMillis", type="bigint", nullable=true)
     */
    private $autoResumeTimeMillis;

    /**
     * @ORM\Column(name="cancelReason", type="integer", nullable=true)
     */
    private $cancelReason;

    /**
     * @ORM\Column(name="cancelSurveyResult_cancelSurveyReason", type="integer", nullable=true)
     */
    private $cancelSurveyResultCancelSurveyReason;

    /**
     * @ORM\Column(name="cancelSurveyResult_userInputCancelReason", type="string", length=255, nullable=true)
     */
    private $cancelSurveyResultUserInputCancelReason;

    /**
     * @ORM\Column(name="countryCode", type="string", length=255, nullable=true)
     */
    private $countryCode;

    /**
     * @ORM\Column(name="developerPayload", type="string", length=255, nullable=true)
     */
    private $developerPayload;

    /**
     * @ORM\Column(name="emailAddress", type="string", length=255, nullable=true)
     */
    private $emailAddress;

    /**
     * @ORM\Column(name="expiryTimeMillis", type="bigint", nullable=true)
     */
    private $expiryTimeMillis;

    /**
     * @ORM\Column(name="expiryTimeUTC", type="datetime", nullable=true)
     */
    private $expiryTimeUTC;

    /**
     * @ORM\Column(name="introductoryPriceInfo_introductoryPriceAmountMicros", type="bigint", nullable=true)
     */
    private $introductoryPriceInfoIntroductoryPriceAmountMicros;

    /**
     * @ORM\Column(name="introductoryPriceInfo_introductoryPriceCurrencyCode", type="string", length=255, nullable=true)
     */
    private $introductoryPriceInfoIntroductoryPriceCurrencyCode;

    /**
     * @ORM\Column(name="introductoryPriceInfo_introductoryPriceCycles", type="integer", nullable=true)
     */
    private $introductoryPriceInfoIntroductoryPriceCycles;

    /**
     * @ORM\Column(name="introductoryPriceInfo_introductoryPricePeriod", type="string", length=255, nullable=true)
     */
    private $introductoryPriceInfoIntroductoryPricePeriod;

    /**
     * @ORM\Column(name="kind", type="string", length=255, nullable=true)
     */
    private $kind;

    /**
     * @ORM\Column(name="linkedPurchaseToken", type="string", length=255, nullable=true)
     */
    private $linkedPurchaseToken;

    /**
     * @ORM\Column(name="orderId", type="string", length=255, nullable=true)
     */
    private $orderId;

    /**
     * @ORM\Column(name="packageName", type="string", length=255, nullable=true)
     */
    private $packageName;

    /**
     * @ORM\Column(name="paymentState", type="integer", nullable=true)
     */
    private $paymentState;

    /**
     * @ORM\Column(name="priceAmountMicros", type="bigint", nullable=true)
     */
    private $priceAmountMicros;

    /**
     * @ORM\Column(name="priceChange_newPrice_currency", type="string", length=255, nullable=true)
     */
    private $priceChangeNewPriceCurrency;

    /**
     * @ORM\Column(name="priceChange_newPrice_priceMicros", type="string", length=255, nullable=true)
     */
    private $priceChangeNewPricePriceMicros;

    /**
     * @ORM\Column(name="priceChange_state", type="integer", nullable=true)
     */
    private $priceChangeState;

    /**
     * @ORM\Column(name="priceCurrencyCode", type="string", length=255, nullable=true)
     */
    private $priceCurrencyCode;

    /**
     * @ORM\Column(name="price_pennies", type="integer", options={"default" : 0})
     */
    private $pricePennies = 0;

    /**
     * @ORM\Column(name="revenue_pennies", type="integer", options={"default" : 0})
     */
    private $revenuePennies = 0;

    /**
     * @ORM\Column(name="profileId", type="string", length=255, nullable=true)
     */
    private $profileId;

    /**
     * @ORM\Column(name="profileName", type="string", length=255, nullable=true)
     */
    private $profileName;

    /**
     * @ORM\Column(name="promotionCode", type="string", length=255, nullable=true)
     */
    private $promotionCode;

    /**
     * @ORM\Column(name="promotionType", type="integer", nullable=true)
     */
    private $promotionType;

    /**
     * @ORM\Column(name="purchaseType", type="integer", nullable=true)
     */
    private $purchaseType;

    /**
     * @ORM\Column(name="startTimeMillis", type="bigint", nullable=true)
     */
    private $startTimeMillis;

    /**
     * @ORM\Column(name="startTimeUTC", type="datetime", nullable=true)
     */
    private $startTimeUTC;

    /**
     * @ORM\Column(name="product_id", type="string", length=255, nullable=true)
     */
    private $productId;

    /**
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(name="notification_type", type="integer", nullable=true)
     */
    private $notificationType;

    /**
     * @ORM\Column(name="notification_translated", type="string", length=255, nullable=true)
     */
    private $notificationTranslated;

    /**
     * @ORM\Column(name="userCancellationTimeMillis", type="bigint", nullable=true)
     */
    private $userCancellationTimeMillis;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="androidSubscriptions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(name="raw_parameters", type="text", nullable=true)
     */
    private $rawParameters;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct(?User $user)
    {
        $this->user = $user;
        $this->createdAt = (new DateTime())->setTimezone(new DateTimeZone('UTC'));
        $this->updatedAt = (new DateTime())->setTimezone(new DateTimeZone('UTC'));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAcknowledgementState(): ?int
    {
        return $this->acknowledgementState;
    }

    public function setAcknowledgementState(?int $acknowledgementState = null): self
    {
        $this->acknowledgementState = $acknowledgementState;

        return $this;
    }

    public function getAutoRenewing(): ?bool
    {
        return $this->autoRenewing;
    }

    public function setAutoRenewing(?bool $autoRenewing = null): self
    {
        $this->autoRenewing = $autoRenewing;

        return $this;
    }

    public function getAutoResumeTimeMillis(): ?int
    {
        return $this->autoResumeTimeMillis;
    }

    public function setAutoResumeTimeMillis(?int $autoResumeTimeMillis = null): self
    {
        $this->autoResumeTimeMillis = $autoResumeTimeMillis;

        return $this;
    }

    public function getCancelReason(): ?int
    {
        return $this->cancelReason;
    }

    public function setCancelReason(?int $cancelReason = null): self
    {
        $this->cancelReason = $cancelReason;

        return $this;
    }

    public function getCancelSurveyReason(): ?int
    {
        return $this->cancelSurveyResultCancelSurveyReason;
    }

    public function setCancelSurveyReason(?int $cancelSurveyResultCancelSurveyReason = null): self
    {
        $this->cancelSurveyResultCancelSurveyReason = $cancelSurveyResultCancelSurveyReason;

        return $this;
    }

    public function getUserInputCancelReason(): ?string
    {
        return $this->cancelSurveyResultUserInputCancelReason;
    }

    public function setUserInputCancelReason(?string $cancelSurveyResultUserInputCancelReason = null): self
    {
        $this->cancelSurveyResultUserInputCancelReason = $cancelSurveyResultUserInputCancelReason;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode = null): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getDeveloperPayload(): ?string
    {
        return $this->developerPayload;
    }

    public function setDeveloperPayload(?string $developerPayload = null): self
    {
        $this->developerPayload = $developerPayload;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(?string $emailAddress = null): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getExpiryTimeMillis(): ?int
    {
        return $this->expiryTimeMillis;
    }

    public function setExpiryTimeMillis(?int $expiryTimeMillis = null): self
    {
        $this->expiryTimeMillis = $expiryTimeMillis;

        return $this;
    }

    public function getExpiryTimeUTC(): ?DateTimeInterface
    {
        return $this->expiryTimeUTC;
    }

    public function setExpiryTimeUTC(?DateTimeInterface $expiryTimeUTC = null): self
    {
        $this->expiryTimeUTC = $expiryTimeUTC;

        return $this;
    }

    public function getIntroductoryPriceAmountMicros(): ?int
    {
        return $this->introductoryPriceInfoIntroductoryPriceAmountMicros;
    }

    public function setIntroductoryPriceAmountMicros(?int $introductoryPriceInfoIntroductoryPriceAmountMicros = null): self
    {
        $this->introductoryPriceInfoIntroductoryPriceAmountMicros = $introductoryPriceInfoIntroductoryPriceAmountMicros;

        return $this;
    }

    public function getIntroductoryPriceCurrencyCode(): ?string
    {
        return $this->introductoryPriceInfoIntroductoryPriceCurrencyCode;
    }

    public function setIntroductoryPriceCurrencyCode(?string $introductoryPriceInfoIntroductoryPriceCurrencyCode = null): self
    {
        $this->introductoryPriceInfoIntroductoryPriceCurrencyCode = $introductoryPriceInfoIntroductoryPriceCurrencyCode;

        return $this;
    }

    public function getIntroductoryPriceCycles(): ?int
    {
        return $this->introductoryPriceInfoIntroductoryPriceCycles;
    }

    public function setIntroductoryPriceCycles(?int $introductoryPriceInfoIntroductoryPriceCycles = null): self
    {
        $this->introductoryPriceInfoIntroductoryPriceCycles = $introductoryPriceInfoIntroductoryPriceCycles;

        return $this;
    }

    public function getIntroductoryPricePeriod(): ?string
    {
        return $this->introductoryPriceInfoIntroductoryPricePeriod;
    }

    public function setIntroductoryPricePeriod(?string $introductoryPriceInfoIntroductoryPricePeriod = null): self
    {
        $this->introductoryPriceInfoIntroductoryPricePeriod = $introductoryPriceInfoIntroductoryPricePeriod;

        return $this;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(?string $kind = null): self
    {
        $this->kind = $kind;

        return $this;
    }

    public function getLinkedPurchaseToken(): ?string
    {
        return $this->linkedPurchaseToken;
    }

    public function setLinkedPurchaseToken(?string $linkedPurchaseToken = null): self
    {
        $this->linkedPurchaseToken = $linkedPurchaseToken;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(?string $orderId = null): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getPackageName(): ?string
    {
        return $this->packageName;
    }

    public function setPackageName(?string $packageName = null): self
    {
        $this->packageName = $packageName;

        return $this;
    }

    public function getPaymentState(): ?int
    {
        return $this->paymentState;
    }

    public function setPaymentState(?int $paymentState = null): self
    {
        $this->paymentState = $paymentState;

        return $this;
    }

    public function getPriceAmountMicros(): ?int
    {
        return $this->priceAmountMicros;
    }

    public function setPriceAmountMicros(?int $priceAmountMicros = null): self
    {
        $this->priceAmountMicros = $priceAmountMicros;

        return $this;
    }

    public function getPriceChangeNewPriceCurrency(): ?string
    {
        return $this->priceChangeNewPriceCurrency;
    }

    public function setPriceChangeNewPriceCurrency(?string $priceChangeNewPriceCurrency): self
    {
        $this->priceChangeNewPriceCurrency = $priceChangeNewPriceCurrency;

        return $this;
    }

    public function getPriceChangeNewPricePriceMicros(): ?string
    {
        return $this->priceChangeNewPricePriceMicros;
    }

    public function setPriceChangeNewPricePriceMicros(?string $priceChangeNewPricePriceMicros = null): self
    {
        $this->priceChangeNewPricePriceMicros = $priceChangeNewPricePriceMicros;

        return $this;
    }

    public function getPriceChangeState(): ?int
    {
        return $this->priceChangeState;
    }

    public function setPriceChangeState(?int $priceChangeState = null): self
    {
        $this->priceChangeState = $priceChangeState;

        return $this;
    }

    public function getPriceCurrencyCode(): ?string
    {
        return $this->priceCurrencyCode;
    }

    public function setPriceCurrencyCode(?string $priceCurrencyCode = null): self
    {
        $this->priceCurrencyCode = $priceCurrencyCode;

        return $this;
    }

    public function getPricePennies(): Money
    {
        return Money::USD($this->pricePennies);
    }

    public function setPricePennies(Money $pricePennies): self
    {
        $this->pricePennies = $pricePennies->getAmount();

        return $this;
    }

    public function getRevenuePennies(): Money
    {
        return Money::USD($this->revenuePennies);
    }

    public function setRevenuePennies(Money $revenuePennies): self
    {
        $this->revenuePennies = $revenuePennies->getAmount();

        return $this;
    }

    public function getProfileId(): ?string
    {
        return $this->profileId;
    }

    public function setProfileId(?string $profileId = null): self
    {
        $this->profileId = $profileId;

        return $this;
    }

    public function getProfileName(): ?string
    {
        return $this->profileName;
    }

    public function setProfileName(?string $profileName = null): self
    {
        $this->profileName = $profileName;

        return $this;
    }

    public function getPromotionCode(): ?string
    {
        return $this->promotionCode;
    }

    public function setPromotionCode(?string $promotionCode = null): self
    {
        $this->promotionCode = $promotionCode;

        return $this;
    }

    public function getPromotionType(): ?int
    {
        return $this->promotionType;
    }

    public function setPromotionType(?int $promotionType = null): self
    {
        $this->promotionType = $promotionType;

        return $this;
    }

    public function getPurchaseType(): ?int
    {
        return $this->purchaseType;
    }

    public function setPurchaseType(?int $purchaseType = null): self
    {
        $this->purchaseType = $purchaseType;

        return $this;
    }

    public function getStartTimeMillis(): ?int
    {
        return $this->startTimeMillis;
    }

    public function setStartTimeMillis(?int $startTimeMillis = null): self
    {
        $this->startTimeMillis = $startTimeMillis;

        return $this;
    }

    public function getStartTimeUTC(): ?DateTimeInterface
    {
        return $this->startTimeUTC;
    }

    public function setStartTimeUTC(?DateTimeInterface $startTimeUTC = null): self
    {
        $this->startTimeUTC = $startTimeUTC;

        return $this;
    }

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(?string $productId = null): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token = null): self
    {
        $this->token = $token;

        return $this;
    }

    public function getUserCancellationTimeMillis(): ?int
    {
        return $this->userCancellationTimeMillis;
    }

    public function setUserCancellationTimeMillis(?int $userCancellationTimeMillis = null): self
    {
        $this->userCancellationTimeMillis = $userCancellationTimeMillis;

        return $this;
    }

    public function getNotificationType(): ?int
    {
        return $this->notificationType;
    }

    public function setNotificationType(?int $notificationType): self
    {
        $this->notificationType = $notificationType;

        return $this;
    }

    public function getNotificationTranslated(): ?string
    {
        return $this->notificationTranslated;
    }

    public function setNotificationTranslated(?string $notificationTranslated): self
    {
        $this->notificationTranslated = $notificationTranslated;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAtPurchaseLifetimeInMonths(): float
    {
        return $this->atPurchaseLifetimeInMonths ?? 0.0;
    }

    public function setAtPurchaseLifetimeInMonths(float $atPurchaseLifetimeInMonths): self
    {
        $this->atPurchaseLifetimeInMonths = $atPurchaseLifetimeInMonths;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getRawParameters(): array
    {
        return json_decode($this->rawParameters, true);
    }

    /**
     * @param mixed[] $rawParameters
     */
    public function setRawParameters(array $rawParameters): self
    {
        $this->rawParameters = json_encode($rawParameters);

        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function updateUpdatedAt(): self
    {
        $this->updatedAt = (new DateTime())->setTimezone(new DateTimeZone('UTC'));

        return $this;
    }

    public function updateUpdatedAtIfNotEqual(self $beforeUpdateAndroidSubscription): self
    {
        if ($beforeUpdateAndroidSubscription->equals($this)) {
            return $this;
        }

        $this->updateUpdatedAt();

        return $this;
    }

    public function isExpired(): bool
    {
        return $this->getExpiryTimeUTC() < new DateTime('now', new DateTimeZone('UTC'));
    }

    public function isTrialPeriod(): bool
    {
        return $this->paymentState === 2;
    }

    public function __clone()
    {
        $this->user = clone $this->user;
    }

    public function equals(self $androidSubscription): bool
    {
        if (!$this->equalsUsers($androidSubscription)
            || !$this->equalsDates($androidSubscription)
        ) {
            return false;
        }

        return $this->getAcknowledgementState() === $androidSubscription->getAcknowledgementState()
            && $this->getAutoRenewing() === $androidSubscription->getAutoRenewing()
            && $this->getAutoResumeTimeMillis() === $androidSubscription->getAutoResumeTimeMillis()
            && $this->getCancelReason() === $androidSubscription->getCancelReason()
            && $this->getCancelSurveyReason() === $androidSubscription->getCancelSurveyReason()
            && $this->getUserInputCancelReason() === $androidSubscription->getUserInputCancelReason()
            && $this->getCountryCode() === $androidSubscription->getCountryCode()
            && $this->getDeveloperPayload() === $androidSubscription->getDeveloperPayload()
            && $this->getEmailAddress() === $androidSubscription->getEmailAddress()
            && $this->getExpiryTimeMillis() === $androidSubscription->getExpiryTimeMillis()
            && $this->getIntroductoryPriceAmountMicros() === $androidSubscription->getIntroductoryPriceAmountMicros()
            && $this->getIntroductoryPriceCurrencyCode() === $androidSubscription->getIntroductoryPriceCurrencyCode()
            && $this->getIntroductoryPriceCycles() === $androidSubscription->getIntroductoryPriceCycles()
            && $this->getIntroductoryPricePeriod() === $androidSubscription->getIntroductoryPricePeriod()
            && $this->getKind() === $androidSubscription->getKind()
            && $this->getOrderId() === $androidSubscription->getOrderId()
            && $this->getPackageName() === $androidSubscription->getPackageName()
            && $this->getPaymentState() === $androidSubscription->getPaymentState()
            && $this->getPriceAmountMicros() === $androidSubscription->getPriceAmountMicros()
            && $this->getPriceChangeNewPriceCurrency() === $androidSubscription->getPriceChangeNewPriceCurrency()
            && $this->getPriceChangeNewPricePriceMicros() === $androidSubscription->getPriceChangeNewPricePriceMicros()
            && $this->getPriceChangeState() === $androidSubscription->getPriceChangeState()
            && $this->getPriceCurrencyCode() === $androidSubscription->getPriceCurrencyCode()
            && $this->getPricePennies()->equals($androidSubscription->getPricePennies())
            && $this->getRevenuePennies()->equals($androidSubscription->getRevenuePennies())
            && $this->getProfileId() === $androidSubscription->getProfileId()
            && $this->getProfileName() === $androidSubscription->getProfileName()
            && $this->getPromotionCode() === $androidSubscription->getPromotionCode()
            && $this->getPromotionType() === $androidSubscription->getPromotionType()
            && $this->getPurchaseType() === $androidSubscription->getPurchaseType()
            && $this->getStartTimeMillis() === $androidSubscription->getStartTimeMillis()
            && $this->getProductId() === $androidSubscription->getProductId()
            && $this->getUserCancellationTimeMillis() === $androidSubscription->getUserCancellationTimeMillis()
            && $this->getNotificationType() === $androidSubscription->getNotificationType()
            && $this->getNotificationTranslated() === $androidSubscription->getNotificationTranslated()
            && $this->getAtPurchaseLifetimeInMonths() === $androidSubscription->getAtPurchaseLifetimeInMonths();
    }

    public function equalsUsers(self $androidSubscription): bool
    {
        if (($this->getUser() === null && $androidSubscription->getUser() instanceof User)
            || ($androidSubscription->getUser() === null && $this->getUser() instanceof User)
        ) {
            return false;
        }

        return $this->getUser()->getId() === $androidSubscription->getUser()->getId();
    }

    public function equalsDates(self $androidSubscription): bool
    {
        $startTimeInTimestamp = $this->getStartTimeUTC() ? $this->getStartTimeUTC()->getTimestamp() : 0;
        $beforeUpdatedStartTimeInTimestamp = $androidSubscription->getStartTimeUTC() ? $androidSubscription->getStartTimeUTC()->getTimestamp() : 0;

        $expiryTimeInTimestamp = $this->getExpiryTimeUTC() ? $this->getExpiryTimeUTC()->getTimestamp() : 0;
        $beforeUpdatedExpiryTimeInTimestamp = $androidSubscription->getExpiryTimeUTC() ? $androidSubscription->getExpiryTimeUTC()->getTimestamp() : 0;

        return $startTimeInTimestamp === $beforeUpdatedStartTimeInTimestamp
            && $expiryTimeInTimestamp === $beforeUpdatedExpiryTimeInTimestamp;
    }
}
