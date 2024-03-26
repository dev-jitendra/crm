<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;

class EmailFilter extends Entity
{
    public const ENTITY_TYPE = 'EmailFilter';

    public const ACTION_SKIP = 'Skip';
    public const ACTION_MOVE_TO_FOLDER = 'Move to Folder';
    public const ACTION_MOVE_TO_GROUP_FOLDER = 'Move to Group Folder';
    public const ACTION_NONE = 'None';

    
    public function getAction(): ?string
    {
        return $this->get('action');
    }

    public function getEmailFolderId(): ?string
    {
        return $this->get('emailFolderId');
    }

    public function getGroupEmailFolderId(): ?string
    {
        return $this->get('groupEmailFolderId');
    }

    public function markAsRead(): bool
    {
        return (bool) $this->get('markAsRead');
    }

    public function isGlobal(): bool
    {
        return (bool) $this->get('isGlobal');
    }

    public function getParentType(): ?string
    {
        return $this->get('parentType');
    }

    public function getParentId(): ?string
    {
        return $this->get('parentId');
    }

    public function getFrom(): ?string
    {
        return $this->get('from');
    }

    public function getTo(): ?string
    {
        return $this->get('to');
    }

    public function getSubject(): ?string
    {
        return $this->get('subject');
    }

    
    public function getBodyContains(): array
    {
        return $this->get('bodyContains') ?? [];
    }

    
    public function getBodyContainsAll(): array
    {
        return $this->get('bodyContainsAll') ?? [];
    }
}
