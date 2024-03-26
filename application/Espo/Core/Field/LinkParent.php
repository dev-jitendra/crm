<?php


namespace Espo\Core\Field;

use Espo\ORM\Entity;
use RuntimeException;


class LinkParent
{
    private string $entityType;
    private string $id;
    private ?string $name = null;

    public function __construct(string $entityType, string $id)
    {
        if (!$entityType) {
            throw new RuntimeException("Empty entity type.");
        }

        if (!$id) {
            throw new RuntimeException("Empty ID.");
        }

        $this->entityType = $entityType;
        $this->id = $id;
    }

    
    public function getId(): string
    {
        return $this->id;
    }

    
    public function getEntityType(): string
    {
        return $this->entityType;
    }

    
    public function getName(): ?string
    {
        return $this->name;
    }

    
    public function withName(?string $name): self
    {
        $obj = new self($this->entityType, $this->id);

        $obj->name = $name;

        return $obj;
    }

    
    public static function create(string $entityType, string $id): self
    {
        return new self($entityType, $id);
    }

    
    public static function createFromEntity(Entity $entity): self
    {
        return new self($entity->getEntityType(), $entity->getId());
    }
}
