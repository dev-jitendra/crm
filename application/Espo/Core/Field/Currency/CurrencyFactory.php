<?php


namespace Espo\Core\Field\Currency;

use Espo\ORM\Entity;
use Espo\ORM\Value\ValueFactory;
use Espo\Core\Field\Currency;
use RuntimeException;

class CurrencyFactory implements ValueFactory
{
    public function isCreatableFromEntity(Entity $entity, string $field): bool
    {
        return $entity->get($field) !== null && $entity->get($field . 'Currency') !== null;
    }

    public function createFromEntity(Entity $entity, string $field): Currency
    {
        if (!$this->isCreatableFromEntity($entity, $field)) {
            throw new RuntimeException();
        }

        return new Currency(
            $entity->get($field),
            $entity->get($field . 'Currency')
        );
    }
}
