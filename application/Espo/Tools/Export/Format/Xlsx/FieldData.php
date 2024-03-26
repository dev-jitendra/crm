<?php


namespace Espo\Tools\Export\Format\Xlsx;

class FieldData
{
    public function __construct(
        private string $entityType,
        private string $field,
        private string $type,
        private ?string $link
    ) {}

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }
}
