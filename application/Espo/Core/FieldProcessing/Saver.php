<?php


namespace Espo\Core\FieldProcessing;

use Espo\ORM\Entity;
use Espo\Core\FieldProcessing\Saver\Params;


interface Saver
{
    
    public function process(Entity $entity, Params $params): void;
}
