<?php


namespace Espo\Core\FieldProcessing;

use Espo\ORM\Entity;

use Espo\Core\FieldProcessing\Loader\Params;


interface Loader
{
    
    public function process(Entity $entity, Params $params): void;
}
