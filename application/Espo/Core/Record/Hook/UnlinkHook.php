<?php


namespace Espo\Core\Record\Hook;

use Espo\ORM\Entity;


interface UnlinkHook
{
    
    public function process(Entity $entity, string $link, Entity $foreignEntity): void;
}
