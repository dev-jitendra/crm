<?php


namespace Espo\Tools\Currency;

use Espo\Core\Acl\Table;
use Espo\Core\Currency\ConfigDataProvider;
use Espo\Core\Currency\Rates;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Acl;
use Espo\Core\Utils\Config\ConfigWriter;
use Espo\Core\Utils\Currency\DatabasePopulator;

class RateService
{
    private const SCOPE = 'Currency';

    public function __construct(
        private ConfigWriter $configWriter,
        private Acl $acl,
        private DatabasePopulator $databasePopulator,
        private ConfigDataProvider $configDataProvider
    ) {}

    
    public function get(): Rates
    {
        if (!$this->acl->check(self::SCOPE)) {
            throw new Forbidden();
        }

        if ($this->acl->getLevel(self::SCOPE, Table::ACTION_READ) !== Table::LEVEL_YES) {
            throw new Forbidden();
        }

        $rates = Rates::create($this->configDataProvider->getBaseCurrency());

        foreach ($this->configDataProvider->getCurrencyList() as $code) {
            $rates = $rates->withRate($code, $this->configDataProvider->getCurrencyRate($code));
        }

        return $rates;
    }

    
    public function set(Rates $rates): void
    {
        if (!$this->acl->check(self::SCOPE)) {
            throw new Forbidden();
        }

        if ($this->acl->getLevel(self::SCOPE, Table::ACTION_EDIT) !== Table::LEVEL_YES) {
            throw new Forbidden();
        }

        $currencyList = $this->configDataProvider->getCurrencyList();
        $baseCurrency = $this->configDataProvider->getBaseCurrency();

        $set = [];

        foreach ($rates->toAssoc() as $key => $value) {
            if ($value < 0) {
                throw new BadRequest("Bad value.");
            }

            if (!in_array($key, $currencyList)) {
                continue;
            }

            if ($key === $baseCurrency) {
                continue;
            }

            $set[$key] = $value;
        }

        foreach ($currencyList as $currency) {
            if ($currency === $baseCurrency) {
                continue;
            }

            $set[$currency] ??= $this->configDataProvider->getCurrencyRate($currency);
        }

        $this->configWriter->set('currencyRates', $set);
        $this->configWriter->save();

        $this->databasePopulator->process();
    }
}
