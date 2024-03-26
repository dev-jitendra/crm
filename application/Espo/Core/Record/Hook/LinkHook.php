<?php


namespace Espo\Core\Record\Hook;

use Espo\ORM\Entity;


interface LinkHook
{
    
    public function process(Entity $entity, string $link, Entity $foreignEntity): void;
}
