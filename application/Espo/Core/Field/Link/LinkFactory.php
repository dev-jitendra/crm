<?php


namespace Espo\Core\Field\Link;

use Espo\ORM\Entity;
use Espo\ORM\Value\ValueFactory;
use Espo\Core\Field\Link;

use RuntimeException;

class LinkFactory implements ValueFactory
{
    public function isCreatableFromEntity(Entity $entity, string $field): bool
    {
        return $entity->get($field . 'Id') !== null;
    }

    public function createFromEntity(Entity $entity, string $field): Link
    {
        if (!$this->isCreatableFromEntity($entity, $field)) {
            throw new RuntimeException();
        }

        $id = $entity->get($field . 'Id');
        $name = $entity->get($field . 'Name');

        return Link
            ::create($id)
            ->withName($name);
    }
}
