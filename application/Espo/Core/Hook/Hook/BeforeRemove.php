<?php


namespace Espo\Core\Hook\Hook;

use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\RemoveOptions;


interface BeforeRemove
{
    
    public function beforeRemove(Entity $entity, RemoveOptions $options): void;
}
