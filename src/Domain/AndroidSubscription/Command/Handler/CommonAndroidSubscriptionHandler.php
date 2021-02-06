<?php

namespace AppBundle\Domain\AndroidSubscription\Command\Handler;

use AppBundle\Domain\AndroidSubscription\Command\CommonAndroidSubscriptionCommand;
use AppBundle\Domain\AndroidSubscription\Entity\AndroidSubscription;
use AppBundle\Domain\AndroidSubscription\Repository\AndroidSubscriptionRepository;
use AppBundle\Domain\AndroidSubscription\Resolver\AndroidSubscriptionRevenueResolver;
use AppBundle\Domain\AndroidSubscription\Resolver\AtPurchaseLifetimeResolver;
use AppBundle\Domain\AndroidSubscription\Resolver\UsPricesResolver;
use DateTime;
use Google_Service_AndroidPublisher_SubscriptionPurchase as AndroidPurchase;
use Money\Money;

abstract class CommonAndroidSubscriptionHandler
{
    protected $androidSubscriptionRepository;
    protected $androidPackageName;
    protected $usPricesResolver;
    protected $androidSubscriptionRevenueResolver;
    protected $atPurchaseLifetimeResolver;

    public function __construct(
        AndroidSubscriptionRepository $androidSubscriptionRepository,
        string $androidPackageName,
        AtPurchaseLifetimeResolver $atPurchaseLifetimeResolver,
        UsPricesResolver $usPricesResolver,
        AndroidSubscriptionRevenueResolver $androidSubscriptionRevenueResolver
    ) {
        $this->androidSubscriptionRepository = $androidSubscriptionRepository;
        $this->androidPackageName = $androidPackageName;
        $this->atPurchaseLifetimeResolver = $atPurchaseLifetimeResolver;
        $this->usPricesResolver = $usPricesResolver;
        $this->androidSubscriptionRevenueResolver = $androidSubscriptionRevenueResolver;
    }

    public function update(AndroidSubscription $androidSubscription, AndroidPurchase $androidPurchase, CommonAndroidSubscriptionCommand $command): void
    {
        $beforeUpdatedAndroidSubscription = clone $androidSubscription;

        $androidSubscription->setProductId($command->getProductId());
        $androidSubscription->setToken($command->getToken());
        $androidSubscription->setOrderId($androidPurchase->getOrderId());

        self::updateNotificationInfo($androidSubscription, $command);

        self::updateAutoRenewInfo($androidSubscription, $androidPurchase);
        self::updateProfileInfo($androidSubscription, $androidPurchase);
        self::updatePromotionInfo($androidSubscription, $androidPurchase);
        self::updateCancellationInfo($androidSubscription, $androidPurchase);
        self::updateStartTimes($androidSubscription, $androidPurchase);
        self::updateExpiryTimes($androidSubscription, $androidPurchase);

        $androidSubscription->setKind($androidPurchase->getKind());
        $androidSubscription->setLinkedPurchaseToken($androidPurchase->getLinkedPurchaseToken());

        $androidSubscription->setPackageName($this->androidPackageName);

        $androidSubscription->setAcknowledgementState($androidPurchase->getAcknowledgementState());
        $androidSubscription->setPurchaseType($androidPurchase->getPurchaseType());
        $androidSubscription->setPaymentState($androidPurchase->getPaymentState());

        $androidSubscription->setDeveloperPayload($androidPurchase->getDeveloperPayload());

        $this->updateAtPurchaseLifetime($androidSubscription);
        $this->updateFinanceInfo($androidSubscription, $androidPurchase);

        $androidSubscription->setRawParameters((array) $androidPurchase->toSimpleObject());

        $androidSubscription->updateUpdatedAtIfNotEqual($beforeUpdatedAndroidSubscription);

        $this->androidSubscriptionRepository->save($androidSubscription);
    }

    private static function updateNotificationInfo(AndroidSubscription $androidSubscription, CommonAndroidSubscriptionCommand $command): void
    {
        $translatedNotification = AndroidSubscription::NOTIFICATION_TYPES[$command->getNotificationType()] ?? 'UNKNOWN';

        $androidSubscription->setNotificationType($command->getNotificationType());
        $androidSubscription->setNotificationTranslated($translatedNotification);
    }

    private static function updateAutoRenewInfo(AndroidSubscription $androidSubscription, AndroidPurchase $androidPurchase): void
    {
        $androidSubscription->setAutoRenewing($androidPurchase->getAutoRenewing());

        $androidSubscription->setAutoResumeTimeMillis($androidPurchase->getAutoResumeTimeMillis());
    }

    private static function updateProfileInfo(AndroidSubscription $androidSubscription, AndroidPurchase $androidPurchase): void
    {
        $androidSubscription->setProfileId($androidPurchase->getProfileId());
        $androidSubscription->setProfileName($androidPurchase->getProfileName());
        $androidSubscription->setEmailAddress($androidPurchase->getEmailAddress());
        $androidSubscription->setCountryCode($androidPurchase->getCountryCode());
    }

    private static function updatePromotionInfo(AndroidSubscription $androidSubscription, AndroidPurchase $androidPurchase): void
    {
        $androidSubscription->setPromotionCode($androidPurchase->getPromotionCode());
        $androidSubscription->setPromotionType($androidPurchase->getPromotionType());
    }

    private function updateAtPurchaseLifetime(AndroidSubscription $androidSubscription): void
    {
        $atPurchaseLifetime = $this->atPurchaseLifetimeResolver->resolveFromAndroidSubscription($androidSubscription);

        $androidSubscription->setAtPurchaseLifetimeInMonths($atPurchaseLifetime->getNewValue());
    }

    private function updateFinanceInfo(AndroidSubscription $androidSubscription, AndroidPurchase $androidPurchase): void
    {
        $androidSubscription->setPriceCurrencyCode($androidPurchase->getPriceCurrencyCode());
        $androidSubscription->setPriceAmountMicros($androidPurchase->getPriceAmountMicros());

        self::updateIntroPriceOffer($androidSubscription, $androidPurchase);
        self::updatePriceChange($androidSubscription, $androidPurchase);

        $this->updateUsdFinanceInfo($androidSubscription);
    }

    private function updateUsdFinanceInfo(AndroidSubscription $androidSubscription): void
    {
        if ($androidSubscription->getPriceAmountMicros() === null || $androidSubscription->isTrialPeriod()) {
            $androidSubscription->setPricePennies(Money::USD(0));
            $androidSubscription->setRevenuePennies(Money::USD(0));

            return;
        }

        $usdPricePennies = $this->usPricesResolver->resolveFromAmountMicros(
            $androidSubscription->getPriceAmountMicros(),
            $androidSubscription->getPriceCurrencyCode(),
            $androidSubscription->getStartTimeUTC()
        );

        $androidSubscription->setPricePennies($usdPricePennies);

        $usdRevenuePennies = $this->androidSubscriptionRevenueResolver->resolveFromAndroidSubscription($androidSubscription);

        $androidSubscription->setRevenuePennies($usdRevenuePennies);
    }

    private static function updateIntroPriceOffer(AndroidSubscription $androidSubscription, AndroidPurchase $androidPurchase): void
    {
        $introPriceOffer = $androidPurchase->getIntroductoryPriceInfo();

        if ($introPriceOffer !== null) {
            $androidSubscription->setIntroductoryPriceAmountMicros($introPriceOffer->getIntroductoryPriceAmountMicros());
            $androidSubscription->setIntroductoryPriceCurrencyCode($introPriceOffer->getIntroductoryPriceCurrencyCode());
            $androidSubscription->setIntroductoryPriceCycles($introPriceOffer->getIntroductoryPriceCycles());
            $androidSubscription->setIntroductoryPricePeriod($introPriceOffer->getIntroductoryPricePeriod());

            return;
        }

        $androidSubscription->setIntroductoryPriceAmountMicros(null);
        $androidSubscription->setIntroductoryPriceCurrencyCode(null);
        $androidSubscription->setIntroductoryPriceCycles(null);
        $androidSubscription->setIntroductoryPricePeriod(null);
    }

    private static function updatePriceChange(AndroidSubscription $androidSubscription, AndroidPurchase $androidPurchase): void
    {
        $priceChange = $androidPurchase->getPriceChange();

        if ($priceChange !== null) {
            $androidSubscription->setPriceChangeNewPriceCurrency($priceChange->getNewPrice()->getCurrency());
            $androidSubscription->setPriceChangeNewPricePriceMicros($priceChange->getNewPrice()->getPriceMicros());
            $androidSubscription->setPriceChangeState($priceChange->getState());

            return;
        }

        $androidSubscription->setPriceChangeNewPriceCurrency('');
        $androidSubscription->setPriceChangeNewPricePriceMicros(null);
        $androidSubscription->setPriceChangeState(null);
    }

    private static function updateCancellationInfo(AndroidSubscription $androidSubscription, AndroidPurchase $androidPurchase): void
    {
        $androidSubscription->setCancelReason($androidPurchase->getCancelReason());
        $androidSubscription->setUserCancellationTimeMillis($androidPurchase->getUserCancellationTimeMillis());

        $cancelSurvey = $androidPurchase->getCancelSurveyResult();

        if ($cancelSurvey !== null) {
            $androidSubscription->setCancelSurveyReason($cancelSurvey->getCancelSurveyReason());
            $androidSubscription->setUserInputCancelReason($cancelSurvey->getUserInputCancelReason());

            return;
        }

        $androidSubscription->setCancelSurveyReason(null);
        $androidSubscription->setUserInputCancelReason(null);
    }

    private static function updateStartTimes(AndroidSubscription $androidSubscription, AndroidPurchase $androidPurchase): void
    {
        $startTimeDate = (new DateTime())->setTimestamp($androidPurchase->getStartTimeMillis() / 1000);
        $androidSubscription->setStartTimeUTC($startTimeDate);

        $androidSubscription->setStartTimeMillis($androidPurchase->getStartTimeMillis());
    }

    private static function updateExpiryTimes(AndroidSubscription $androidSubscription, AndroidPurchase $androidPurchase): void
    {
        $expiryDatetime = (new DateTime())->setTimestamp($androidPurchase->getExpiryTimeMillis() / 1000);
        $androidSubscription->setExpiryTimeUTC($expiryDatetime);

        $androidSubscription->setExpiryTimeMillis($androidPurchase->getExpiryTimeMillis());
    }
}
