<?php


namespace Espo\ORM\Value;

use Espo\ORM\Entity;

class ValueAccessor
{
    public function __construct(
        private Entity $entity,
        private GeneralValueFactory $valueFactory,
        private GeneralAttributeExtractor $extractor
    ) {}

    
    public function get(string $field): ?object
    {
        if (!$this->isGettable($field)) {
            return null;
        }

        return $this->valueFactory->createFromEntity($this->entity, $field);
    }

    
    public function isGettable(string $field): bool
    {
        return $this->valueFactory->isCreatableFromEntity($this->entity, $field);
    }

    
    public function set(string $field, ?object $value): void
    {
        $attributeValueMap = $this->extractor->extract($this->entity->getEntityType(), $field, $value);

        $this->entity->set($attributeValueMap);
    }
}
