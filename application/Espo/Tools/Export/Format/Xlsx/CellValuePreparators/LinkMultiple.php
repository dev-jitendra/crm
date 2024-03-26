<?php


namespace Espo\Tools\Export\Format\Xlsx\CellValuePreparators;

use Espo\ORM\Entity;
use Espo\Tools\Export\Format\CellValuePreparator;
use stdClass;

class LinkMultiple implements CellValuePreparator
{
    public function prepare(Entity $entity, string $name): ?string
    {
        if (
            !$entity->has($name . 'Ids') ||
            !$entity->has($name . 'Names')
        ) {
            return null;
        }

        
        $ids = $entity->get($name . 'Ids');
        
        $names = $entity->get($name . 'Names');

        $nameList = array_map(function ($id) use ($names) {
            return $names->$id ?? $id;
        }, $ids);

        return implode(',', $nameList);
    }
}
