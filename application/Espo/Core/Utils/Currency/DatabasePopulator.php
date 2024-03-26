<?php


namespace Espo\Core\Utils\Currency;

use Espo\Entities\Currency;
use Espo\ORM\EntityManager;
use Espo\Core\Utils\Config;


class DatabasePopulator
{
    public function __construct(
        private Config $config,
        private EntityManager $entityManager)
    {}

    public function process(): void
    {
        $defaultCurrency = $this->config->get('defaultCurrency');
        $baseCurrency = $this->config->get('baseCurrency');
        $currencyRates = $this->config->get('currencyRates');

        if ($defaultCurrency !== $baseCurrency) {
            $currencyRates = $this->exchangeRates($baseCurrency, $defaultCurrency, $currencyRates);
        }

        $currencyRates[$defaultCurrency] = 1.00;

        $delete = $this->entityManager->getQueryBuilder()
            ->delete()
            ->from(Currency::ENTITY_TYPE)
            ->build();

        $this->entityManager->getQueryExecutor()->execute($delete);

        foreach ($currencyRates as $currencyName => $rate) {
            $this->entityManager->createEntity(Currency::ENTITY_TYPE, [
                'id' => $currencyName,
                'rate' => $rate,
            ]);
        }
    }

    
    private function exchangeRates(string $baseCurrency, string $defaultCurrency, array $currencyRates): array
    {
        $precision = 5;
        $defaultCurrencyRate = round(1 / $currencyRates[$defaultCurrency], $precision);

        $exchangedRates = [];
        $exchangedRates[$baseCurrency] = $defaultCurrencyRate;

        unset($currencyRates[$baseCurrency], $currencyRates[$defaultCurrency]);

        foreach ($currencyRates as $currencyName => $rate) {
            $exchangedRates[$currencyName] = round($rate * $defaultCurrencyRate, $precision);
        }

        return $exchangedRates;
    }
}
