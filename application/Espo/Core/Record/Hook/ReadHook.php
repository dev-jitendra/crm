<?php


namespace Espo\Core\Record\Hook;

use Espo\ORM\Entity;
use Espo\Core\Record\ReadParams;


interface ReadHook
{
    
    public function process(Entity $entity, ReadParams $params): void;
}
