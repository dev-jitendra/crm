<?php


namespace Espo\Classes\FieldValidators;

use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;

class UrlType
{
    private Metadata $metadata;

    private VarcharType $varcharType;

    public function __construct(Metadata $metadata, VarcharType $varcharType)
    {
        $this->metadata = $metadata;
        $this->varcharType = $varcharType;
    }

    public function checkRequired(Entity $entity, string $field): bool
    {
        return $this->varcharType->checkRequired($entity, $field);
    }

    public function checkMaxLength(Entity $entity, string $field, ?int $validationValue): bool
    {
        return $this->varcharType->checkMaxLength($entity, $field, $validationValue);
    }

    public function checkValid(Entity $entity, string $field): bool
    {
        $value = $entity->get($field);

        if ($value === null) {
            return true;
        }

        
        $pattern = $this->metadata->get(['app', 'regExpPatterns', 'uriOptionalProtocol', 'pattern']);

        $preparedPattern = '/^' . $pattern . '$/';

        return (bool) preg_match($preparedPattern, $value);
    }
}
