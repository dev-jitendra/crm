<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;

class Floating implements CellValuePreparator
{
    public function prepare(Entity $entity, string $name): float
    {
        return $entity->get($name) ?? 0.0;
    }
}
