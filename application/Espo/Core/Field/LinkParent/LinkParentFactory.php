<?php


namespace Espo\Core\Field\LinkParent;

use Espo\ORM\Entity;
use Espo\ORM\Value\ValueFactory;
use Espo\Core\Field\LinkParent;

use RuntimeException;

class LinkParentFactory implements ValueFactory
{
    public function isCreatableFromEntity(Entity $entity, string $field): bool
    {
        return $entity->get($field . 'Id') !== null && $entity->get($field . 'Type') !== null;
    }

    public function createFromEntity(Entity $entity, string $field): LinkParent
    {
        if (!$this->isCreatableFromEntity($entity, $field)) {
            throw new RuntimeException();
        }

        $id = $entity->get($field . 'Id');
        $entityType = $entity->get($field . 'Type');

        return LinkParent
            ::create($entityType, $id)
            ->withName($entity->get($field . 'Name'));
    }
}
