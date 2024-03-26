<?php


namespace Espo\Tools\EmailTemplate;

use Espo\ORM\Entity;
use Espo\Entities\User;

class Data
{
    
    private $entityHash = [];
    private ?string $emailAddress = null;
    private ?Entity $parent = null;
    private ?string $parentId = null;
    private ?string $parentType = null;
    private ?string $relatedId = null;
    private ?string $relatedType = null;
    private ?User $user = null;

    
    public function getEntityHash(): array
    {
        return $this->entityHash;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function getParent(): ?Entity
    {
        return $this->parent;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function getParentType(): ?string
    {
        return $this->parentType;
    }

    public function getRelatedId(): ?string
    {
        return $this->relatedId;
    }

    public function getRelatedType(): ?string
    {
        return $this->relatedType;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    
    public function withEntityHash(array $entityHash): self
    {
        $obj = clone $this;
        $obj->entityHash = $entityHash;

        return $obj;
    }

    
    public function withEmailAddress(?string $emailAddress): self
    {
        $obj = clone $this;
        $obj->emailAddress = $emailAddress;

        return $obj;
    }

    public function withParent(?Entity $parent): self
    {
        $obj = clone $this;
        $obj->parent = $parent;

        return $obj;
    }

    public function withParentId(?string $parentId): self
    {
        $obj = clone $this;
        $obj->parentId = $parentId;

        return $obj;
    }

    public function withParentType(?string $parentType): self
    {
        $obj = clone $this;
        $obj->parentType = $parentType;

        return $obj;
    }

    public function withRelatedId(?string $relatedId): self
    {
        $obj = clone $this;
        $obj->relatedId = $relatedId;

        return $obj;
    }

    public function withRelatedType(?string $relatedType): self
    {
        $obj = clone $this;
        $obj->relatedType = $relatedType;

        return $obj;
    }

    public static function create(): self
    {
        return new self();
    }

    
    public function withUser(?User $user): self
    {
        $obj = clone $this;
        $obj->user = $user;

        return $obj;
    }
}
