<?php


namespace Espo\Tools\Attachment;

use Espo\Core\Exceptions\Error;


class FieldData
{
    private string $field;
    private ?string $parentType;
    private ?string $relatedType;

    
    public function __construct(
        string $field,
        ?string $parentType,
        ?string $relatedType
    ) {
        $this->field = $field;
        $this->parentType = $parentType;
        $this->relatedType = $relatedType;

        if (!$parentType && !$relatedType) {
            throw new Error("No parentType and relatedType.");
        }
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getParentType(): ?string
    {
        return $this->parentType;
    }

    public function getRelatedType(): ?string
    {
        return $this->relatedType;
    }
}
