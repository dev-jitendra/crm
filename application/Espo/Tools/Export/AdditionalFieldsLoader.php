<?php


namespace Espo\Tools\Export;

use Espo\ORM\Entity;

interface AdditionalFieldsLoader
{
    
    public function load(Entity $entity, array $fieldList): void;
}
