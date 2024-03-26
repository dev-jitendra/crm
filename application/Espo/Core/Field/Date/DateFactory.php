<?php


namespace Espo\Core\Field\Date;

use Espo\ORM\Entity;
use Espo\ORM\Value\ValueFactory;

use Espo\Core\Field\Date;

use RuntimeException;

class DateFactory implements ValueFactory
{
    public function isCreatableFromEntity(Entity $entity, string $field): bool
    {
        return $entity->get($field) !== null;
    }

    public function createFromEntity(Entity $entity, string $field): Date
    {
        if (!$this->isCreatableFromEntity($entity, $field)) {
            throw new RuntimeException();
        }

        return new Date(
            $entity->get($field)
        );
    }
}
