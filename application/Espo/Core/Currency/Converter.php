<?php


namespace Espo\Core\Currency;

use Espo\Core\Field\Currency;

use RuntimeException;


class Converter
{
    private ConfigDataProvider $configDataProvider;

    public function __construct(ConfigDataProvider $configDataProvider)
    {
        $this->configDataProvider = $configDataProvider;
    }

    
    public function convert(Currency $value, string $targetCurrencyCode): Currency
    {
        if (!$this->configDataProvider->hasCurrency($targetCurrencyCode)) {
            throw new RuntimeException("Can't convert currency to unknown currency '{$targetCurrencyCode}.");
        }

        $rate = $this->configDataProvider->getCurrencyRate($value->getCode());
        $targetRate = $this->configDataProvider->getCurrencyRate($targetCurrencyCode);

        $convertedAmount = $this->convertAmount($value->getAmountAsString(), $rate, $targetRate);

        return new Currency($convertedAmount, $targetCurrencyCode);
    }

    
    public function convertWithRates(Currency $value, string $targetCurrencyCode, Rates $rates): Currency
    {
        $currencyCode = $value->getCode();

        if (!$rates->hasRate($currencyCode)) {
            throw new RuntimeException("No rate for the currency '{$currencyCode}.");
        }

        if (!$rates->hasRate($targetCurrencyCode)) {
            throw new RuntimeException("No rate for the currency '{$targetCurrencyCode}.");
        }

        $rate = $rates->getRate($currencyCode);
        $targetRate = $rates->getRate($targetCurrencyCode);

        $convertedAmount = $this->convertAmount($value->getAmountAsString(), $rate, $targetRate);

        return new Currency($convertedAmount, $targetCurrencyCode);
    }

    
    public function convertToDefault(Currency $value): Currency
    {
        $targetCurrencyCode = $this->configDataProvider->getDefaultCurrency();

        return $this->convert($value, $targetCurrencyCode);
    }

    private function convertAmount(string $amount, float $rate, float $targetRate): string
    {
        return CalculatorUtil::divide(
            CalculatorUtil::multiply($amount, (string) $rate),
            (string) $targetRate
        );
    }
}
