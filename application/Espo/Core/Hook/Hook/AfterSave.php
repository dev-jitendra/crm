<?php


namespace Espo\Core\Hook\Hook;

use Espo\ORM\Entity;
use Espo\ORM\Repository\Option\SaveOptions;


interface AfterSave
{
    
    public function afterSave(Entity $entity, SaveOptions $options): void;
}
