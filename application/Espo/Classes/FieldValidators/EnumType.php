<?php


namespace Espo\Classes\FieldValidators;

use Espo\Core\Utils\Metadata;

use Espo\ORM\Defs;
use Espo\ORM\Entity;

class EnumType
{
    private Metadata $metadata;
    private Defs $defs;

    private const DEFAULT_MAX_LENGTH = 255;

    public function __construct(Metadata $metadata, Defs $defs)
    {
        $this->metadata = $metadata;
        $this->defs = $defs;
    }

    public function checkRequired(Entity $entity, string $field): bool
    {
        return $this->isNotEmpty($entity, $field);
    }

    public function checkValid(Entity $entity, string $field): bool
    {
        if (!$entity->has($field)) {
            return true;
        }

        $fieldDefs = $this->defs
            ->getEntity($entity->getEntityType())
            ->getField($field);

        
        $path = $fieldDefs->getParam('optionsPath');
        
        $ref = $fieldDefs->getParam('optionsReference');

        if (!$path && $ref && str_contains($ref, '.')) {
            [$refEntityType, $refField] = explode('.', $ref);

            $path = "entityDefs.{$refEntityType}.fields.{$refField}.options";
        }

        
        $optionList = $path ?
            $this->metadata->get($path) :
            $fieldDefs->getParam('options');

        if ($optionList === null) {
            return true;
        }

        
        if ($optionList === false) {
            return true;
        }

        $optionList = array_map(
            fn ($item) => $item === '' ? null : $item,
            $optionList
        );

        $value = $entity->get($field);

        
        
        if ($value === '') {
            $value = null;
        }

        return in_array($value, $optionList);
    }

    public function checkMaxLength(Entity $entity, string $field, ?int $validationValue): bool
    {
        if (!$this->isNotEmpty($entity, $field)) {
            return true;
        }

        $value = $entity->get($field);

        $maxLength = $validationValue ?? self::DEFAULT_MAX_LENGTH;

        if (mb_strlen($value) > $maxLength) {
            return false;
        }

        return true;
    }

    protected function isNotEmpty(Entity $entity, string $field): bool
    {
        return $entity->has($field) && $entity->get($field) !== null;
    }
}
