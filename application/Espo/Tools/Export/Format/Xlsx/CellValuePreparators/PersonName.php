<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;

class PersonName implements CellValuePreparator
{
    public function prepare(Entity $entity, string $name): ?string
    {
        $value = $entity->get($name);

        if ($value) {
            return $value;
        }

        $arr = [];

        $firstName = $entity->get('first' . ucfirst($name));
        $lastName = $entity->get('last' . ucfirst($name));

        if ($firstName) {
            $arr[] = $firstName;
        }

        if ($lastName) {
            $arr[] = $lastName;
        }

        return implode(' ', $arr) ?: null;
    }
}
