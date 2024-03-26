<?php


namespace Espo\Core\Field\DateTime;

use Espo\ORM\Entity;
use Espo\ORM\Value\ValueFactory;
use Espo\Core\Field\DateTime;

use RuntimeException;

class DateTimeFactory implements ValueFactory
{
    public function isCreatableFromEntity(Entity $entity, string $field): bool
    {
        return $entity->get($field) !== null;
    }

    public function createFromEntity(Entity $entity, string $field): DateTime
    {
        if (!$this->isCreatableFromEntity($entity, $field)) {
            throw new RuntimeException();
        }

        return new DateTime(
            $entity->get($field)
        );
    }
}
