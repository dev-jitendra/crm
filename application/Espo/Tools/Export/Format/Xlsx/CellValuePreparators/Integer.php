<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;

class Integer implements CellValuePreparator
{
    public function prepare(Entity $entity, string $name): int
    {
        
        return $entity->get($name) ?? 0;
    }
}
