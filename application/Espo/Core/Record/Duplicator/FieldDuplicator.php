<?php


namespace Espo\Core\Record\Duplicator;

use Espo\ORM\Entity;

use stdClass;


interface FieldDuplicator
{
    public function duplicate(Entity $entity, string $field): stdClass;
}
