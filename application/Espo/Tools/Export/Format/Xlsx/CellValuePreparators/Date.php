<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\Core\Field\Date as DateValue;
use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;

class Date implements CellValuePreparator
{
    public function prepare(Entity $entity, string $name): ?DateValue
    {
        $value = $entity->get($name);

        if (!$value) {
            return null;
        }

        return DateValue::fromString($value);
    }
}
