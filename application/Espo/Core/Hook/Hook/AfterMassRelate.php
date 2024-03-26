<?php


namespace Espo\Core\Hook\Hook;

use Espo\ORM\Entity;
use Espo\ORM\Query\Select;
use Espo\ORM\Repository\Option\MassRelateOptions;


interface AfterMassRelate
{
    
    public function afterMassRelate(
        Entity $entity,
        string $relationName,
        Select $query,
        array $columnData,
        MassRelateOptions $options
    ): void;
}
