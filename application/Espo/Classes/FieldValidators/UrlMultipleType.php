<?php


namespace Espo\Classes\FieldValidators;

use Espo\ORM\Entity;

class UrlMultipleType extends ArrayType
{
    private const MAX_ITEM_LENGTH = 255;

    public function checkNoEmptyString(Entity $entity, string $field, ?bool $validationValue): bool
    {
        return parent::checkNoEmptyString($entity, $field, true);
    }

    public function checkMaxItemLength(Entity $entity, string $field, ?int $validationValue): bool
    {
        return parent::checkMaxItemLength($entity, $field, self::MAX_ITEM_LENGTH);
    }

    public function checkPattern(Entity $entity, string $field, ?string $validationValue): bool
    {
        
        $pattern = $this->metadata->get(['app', 'regExpPatterns', 'uriOptionalProtocol', 'pattern']);

        return parent::checkPattern($entity, $field, $pattern);
    }
}
