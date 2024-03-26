<?php


namespace Espo\Tools\EmailNotification;

class AssignmentProcessorData
{
    private ?string $userId = null;

    private ?string $assignerUserId = null;

    private ?string $entityId = null;

    private ?string $entityType = null;

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getAssignerUserId(): ?string
    {
        return $this->assignerUserId;
    }

    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    public function getEntityType(): ?string
    {
        return $this->entityType;
    }

    public static function create(): self
    {
        return new self();
    }

    public function withUserId(string $userId): self
    {
        $obj = clone $this;
        $obj->userId = $userId;

        return $obj;
    }

    public function withAssignerUserId(string $assignerUserId): self
    {
        $obj = clone $this;
        $obj->assignerUserId = $assignerUserId;

        return $obj;
    }

    public function withEntityId(string $entityId): self
    {
        $obj = clone $this;
        $obj->entityId = $entityId;

        return $obj;
    }

    public function withEntityType(string $entityType): self
    {
        $obj = clone $this;
        $obj->entityType = $entityType;

        return $obj;
    }
}
