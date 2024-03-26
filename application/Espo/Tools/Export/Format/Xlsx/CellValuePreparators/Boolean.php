<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;

class Boolean implements CellValuePreparator
{
    public function prepare(Entity $entity, string $name): bool
    {
        return (bool) $entity->get($name);
    }
}
