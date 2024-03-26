<?php


namespace Espo\Core\Hook\Hook;

use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\SaveOptions;


interface BeforeSave
{
    
    public function beforeSave(Entity $entity, SaveOptions $options): void;
}
