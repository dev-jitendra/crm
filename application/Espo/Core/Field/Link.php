<?php


namespace Espo\Core\Field;

use RuntimeException;


class Link
{
    private string $id;
    private ?string $name = null;

    public function __construct(string $id)
    {
        if (!$id) {
            throw new RuntimeException("Empty ID.");
        }

        $this->id = $id;
    }

    
    public function getId(): string
    {
        return $this->id;
    }

    
    public function getName(): ?string
    {
        return $this->name;
    }

    
    public function withName(?string $name): self
    {
        $obj = new self($this->id);

        $obj->name = $name;

        return $obj;
    }

    
    public static function create(string $id): self
    {
        return new self($id);
    }
}
