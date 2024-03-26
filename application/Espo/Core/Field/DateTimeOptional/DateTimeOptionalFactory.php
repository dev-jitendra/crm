<?php


namespace Espo\Core\Field\DateTimeOptional;

use Espo\ORM\Entity;
use Espo\ORM\Value\ValueFactory;
use Espo\Core\Field\DateTimeOptional;

use RuntimeException;

class DateTimeOptionalFactory implements ValueFactory
{
    public function isCreatableFromEntity(Entity $entity, string $field): bool
    {
        return $entity->get($field) !== null || $entity->get($field . 'Date') !== null;
    }

    public function createFromEntity(Entity $entity, string $field): DateTimeOptional
    {
        if (!$this->isCreatableFromEntity($entity, $field)) {
            throw new RuntimeException();
        }

        $stringValue = $entity->get($field . 'Date') ?? $entity->get($field);

        return new DateTimeOptional($stringValue);
    }
}
