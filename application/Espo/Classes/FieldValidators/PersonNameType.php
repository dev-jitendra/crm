<?php


namespace Espo\Classes\FieldValidators;

use Espo\ORM\Entity;
use Espo\Core\Utils\FieldUtil;

class PersonNameType
{
    public function __construct(private FieldUtil $fieldUtil)
    {}

    public function checkRequired(Entity $entity, string $field): bool
    {
        $isEmpty = true;

        $attributeList = $this->fieldUtil->getActualAttributeList($entity->getEntityType(), $field);

        foreach ($attributeList as $attribute) {
            if ($attribute === 'salutation' . ucfirst($field)) {
                continue;
            }

            if ($entity->has($attribute) && $entity->get($attribute) !== '') {
                $isEmpty = false;

                break;
            }
        }

        if ($isEmpty) {
            return false;
        }

        return true;
    }
}
