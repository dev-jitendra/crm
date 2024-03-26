<?php


namespace Espo\Modules\Crm\Tools\Activities;

class FetchParams
{
    private ?int $maxSize;
    private ?int $offset;
    private ?string $entityType;

    public function __construct(
        ?int $maxSize,
        ?int $offset,
        ?string $entityType
    ) {
        $this->maxSize = $maxSize;
        $this->offset = $offset;
        $this->entityType = $entityType;
    }

    public function getMaxSize(): ?int
    {
        return $this->maxSize;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getEntityType(): ?string
    {
        return $this->entityType;
    }
}
