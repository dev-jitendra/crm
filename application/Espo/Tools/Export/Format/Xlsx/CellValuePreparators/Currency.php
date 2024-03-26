<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\Core\Field\Currency as CurrencyValue;
use Espo\Core\Field\Currency\CurrencyFactory;
use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;

class Currency implements CellValuePreparator
{
    public function prepare(Entity $entity, string $name): ?CurrencyValue
    {
        $factory = new CurrencyFactory();

        if (!$factory->isCreatableFromEntity($entity, $name)) {
            return null;
        }

        return $factory->createFromEntity($entity, $name);
    }
}
