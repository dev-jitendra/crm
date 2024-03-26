<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;

class General implements CellValuePreparator
{
    public function prepare(Entity $entity, string $name): string|bool|int|float|null
    {
        $value = $entity->get($name);

        if ($value === null) {
            return null;
        }

        if (
            !is_string($value) &&
            !is_int($value) &&
            !is_float($value) &&
            !is_bool($value)
        ) {
            return null;
        }

        return $value;
    }
}
