<?php


namespace Espo\Core\Hook\Hook;

use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\RemoveOptions;


interface AfterRemove
{
    
    public function afterRemove(Entity $entity, RemoveOptions $options): void;
}
