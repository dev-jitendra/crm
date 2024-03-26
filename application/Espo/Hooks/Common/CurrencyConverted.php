<?php


namespace Espo\Hooks\Common;

use Espo\ORM\Entity;
use Espo\Core\Di;

class CurrencyConverted implements Di\MetadataAware, Di\ConfigAware
{
    use Di\MetadataSetter;
    use Di\ConfigSetter;

    public static int $order = 1;

    public function beforeSave(Entity $entity): void
    {
        $fieldDefs = $this->metadata->get(['entityDefs', $entity->getEntityType(), 'fields'], []);

        foreach ($fieldDefs as $fieldName => $defs) {
            if (empty($defs['type']) || $defs['type'] !== 'currencyConverted') {
                continue;
            }

            $currencyFieldName = substr($fieldName, 0, -9);

            $currencyCurrencyFieldName = $currencyFieldName . 'Currency';

            if (
                !$entity->isAttributeChanged($currencyFieldName) &&
                !$entity->isAttributeChanged($currencyCurrencyFieldName)
            ) {
                continue;
            }

            if (empty($fieldDefs[$currencyFieldName])) {
                continue;
            }

            if ($entity->get($currencyFieldName) === null) {
                $entity->set($fieldName, null);

                continue;
            }

            $currency = $entity->get($currencyCurrencyFieldName);
            $value = $entity->get($currencyFieldName);

            if (!$currency) {
                continue;
            }

            $rates = $this->config->get('currencyRates', []);
            $baseCurrency = $this->config->get('baseCurrency');
            $defaultCurrency = $this->config->get('defaultCurrency');

            if ($defaultCurrency === $currency) {
                $targetValue = $value;
            }
            else {
                $targetValue = $value;
                $targetValue = $targetValue / (isset($rates[$baseCurrency]) ? $rates[$baseCurrency] : 1.0);
                $targetValue = $targetValue * (isset($rates[$currency]) ? $rates[$currency] : 1.0);

                $targetValue = round($targetValue, 2);
            }

            $entity->set($fieldName, $targetValue);
        }
    }
}
