<?php


namespace Espo\Classes\FieldValidators;

use Espo\Core\Field\Currency;
use Espo\Core\Utils\Config;
use Espo\ORM\BaseEntity;
use Espo\ORM\Entity;

class CurrencyType extends FloatType
{
    private const DEFAULT_PRECISION = 13;

    public function __construct(private Config $config) {}

    protected function isNotEmpty(Entity $entity, string $field): bool
    {
        return
            $entity->has($field) && $entity->get($field) !== null &&
            $entity->has($field . 'Currency') && $entity->get($field . 'Currency') !== null &&
            $entity->get($field . 'Currency') !== '';
    }

    public function checkValid(Entity $entity, string $field): bool
    {
        if (!$this->isNotEmpty($entity, $field)) {
            return true;
        }

        if ($entity->getAttributeType($field) !== Entity::VARCHAR) {
            return true;
        }

        
        $value = $entity->get($field);

        if (preg_match('/^-?[0-9]+\.?[0-9]*$/', $value)) {
            return true;
        }

        return false;
    }

    public function checkInPermittedRange(Entity $entity, string $field): bool
    {
        if (!$this->isNotEmpty($entity, $field)) {
            return true;
        }

        if ($entity->getAttributeType($field) !== Entity::VARCHAR) {
            return true;
        }

        if (!$entity instanceof BaseEntity) {
            return true;
        }

        
        $precision = $entity->getAttributeParam($field, 'precision') ?? self::DEFAULT_PRECISION;

        $value = $entity->get($field);

        $currency = Currency::create($value, 'USD');

        if ($currency->isNegative()) {
            $currency = $currency->multiply(-1);
        }

        $pad = str_pad('', $precision, '9');

        $limit = Currency::create($pad, 'USD');

        if ($currency->compare($limit) === 1) {
            return false;
        }

        return true;
    }

    public function checkValidCurrency(Entity $entity, string $field): bool
    {
        $attribute = $field . 'Currency';

        if (!$entity->has($attribute)) {
            return true;
        }

        $currency = $entity->get($attribute);
        $currencyList = $this->config->get('currencyList') ?? [$this->config->get('defaultCurrency')];

        if (
            $currency === null &&
            !$entity->has($field) &&
            $entity->isNew()
        ) {
            return true;
        }

        if (
            $currency === null &&
            $entity->has($field) &&
            $entity->get($field) === null
        ) {
            return true;
        }

        return in_array($currency, $currencyList);
    }
}
