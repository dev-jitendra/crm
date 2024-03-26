<?php


namespace Espo\Core\Record\ActionHistory;

use Espo\ORM\Entity;


interface ActionLogger
{
    
    public function log(string $action, Entity $entity): void;
}
