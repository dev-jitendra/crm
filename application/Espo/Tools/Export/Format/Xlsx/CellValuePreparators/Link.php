<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;

class Link implements CellValuePreparator
{
    public function prepare(Entity $entity, string $name): ?string
    {
        
        return $entity->get($name . 'Name');
    }
}
