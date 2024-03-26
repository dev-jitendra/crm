<?php


namespace Espo\Core\Duplicate;

use Espo\ORM\Entity;
use Espo\ORM\Query\Part\WhereItem;


interface WhereBuilder
{
    
    public function build(Entity $entity): ?WhereItem;
}
