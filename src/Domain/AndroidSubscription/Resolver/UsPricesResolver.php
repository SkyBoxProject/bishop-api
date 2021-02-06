<?php

namespace AppBundle\Domain\AndroidSubscription\Resolver;

use AppBundle\Domain\ExchangeRate\Entity\ExchangeRate;
use AppBundle\Domain\ExchangeRate\Repository\ExchangeRateRepository;
use AppBundle\Module\Money\MoneyConverter;
use DateTimeInterface;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;

/**
 * @link https://developers.google.com/android-publisher/api-ref/rest/v3/purchases.subscriptions#priceAmountMicros
 */
final class UsPricesResolver
{
    private const MINOR_UNIT = 1000000;

    private $exchangeRateRepository;
    private $moneyConverter;

    public function __construct(ExchangeRateRepository $exchangeRateRepository, MoneyConverter $moneyConverter)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->moneyConverter = $moneyConverter;
    }

    public function resolveFromAmountMicros(string $amount, string $currencyCode, DateTimeInterface $purchaseDate): Money
    {
        $exchangeRate = $this->exchangeRateRepository->getOneByCurrencyAndDate(new Currency($currencyCode), $purchaseDate);

        $price = self::resolveMoney($amount, $currencyCode);

        return $this->convertAsPerExchangeRateInUSD($price, $exchangeRate);
    }

    private static function resolveMoney(string $amount, string $currencyCode): Money
    {
        $currencies = new ISOCurrencies();

        $currency = new Currency($currencyCode);

        $subunit = $currencies->subunitFor($currency);

        return new Money(($amount / self::MINOR_UNIT) * (10 ** $subunit), $currency);
    }

    /**
     * @externalDoc Price is expressed in micro-units, where 1,000,000 micro-units represents one unit of the currency.
     *
     * @link https://developers.google.com/android-publisher/api-ref/rest/v3/purchases.subscriptions#priceAmountMicros
     */
    private function convertAsPerExchangeRateInUSD(Money $price, ExchangeRate $exchangeRate): Money
    {
        return $this->moneyConverter->convertAsPerExchangeRateInUSD($price, $exchangeRate);
    }
}
