<?php


namespace Espo\Core\Currency;

use Espo\Core\Utils\Config;
use RuntimeException;

class ConfigDataProvider
{
    public function __construct(private Config $config)
    {}

    
    public function getDefaultCurrency(): string
    {
        return $this->config->get('defaultCurrency');
    }

    
    public function getBaseCurrency(): string
    {
        return $this->config->get('baseCurrency');
    }

    
    public function getCurrencyList(): array
    {
        return $this->config->get('currencyList') ?? [];
    }

    
    public function hasCurrency(string $currencyCode): bool
    {
        return in_array($currencyCode, $this->getCurrencyList());
    }

    
    public function getCurrencyRate(string $currencyCode): float
    {
        $rates = $this->config->get('currencyRates') ?? [];

        if (!$this->hasCurrency($currencyCode)) {
            throw new RuntimeException("Can't get currency rate of '{$currencyCode}' currency.");
        }

        return $rates[$currencyCode] ?? 1.0;
    }

    
    public function getCurrencyRates(): Rates
    {
        $rates = $this->config->get('currencyRates') ?? [];

        $rates[$this->getBaseCurrency()] = 1.0;

        return Rates::fromAssoc($rates, $this->getBaseCurrency());
    }
}
