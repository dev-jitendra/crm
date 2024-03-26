<?php


namespace Espo\Classes\FieldValidators\Attachment;

use Espo\Classes\FieldValidators\LinkParentType;
use Espo\ORM\Entity;

class Related extends LinkParentType
{
    public function checkValid(Entity $entity, string $field): bool
    {
        $typeValue = $entity->get($field . 'Type');

        if ($typeValue === 'TemplateManager') {
            return true;
        }

        return parent::checkValid($entity, $field);
    }
}
