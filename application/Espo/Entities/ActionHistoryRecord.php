<?php


namespace Espo\Entities;

use Espo\Core\Field\LinkParent;
use Espo\Core\ORM\Entity;
use Espo\Core\Record\ActionHistory\Action;

class ActionHistoryRecord extends Entity
{
    public const ENTITY_TYPE = 'ActionHistoryRecord';

    public const ACTION_CREATE = Action::CREATE;
    public const ACTION_READ = Action::READ;
    public const ACTION_UPDATE = Action::UPDATE;
    public const ACTION_DELETE = Action::DELETE;

    
    public function setAction(string $action): self
    {
        $this->set('action', $action);

        return $this;
    }

    public function setUserId(string $userId): self
    {
        $this->set('userId', $userId);

        return $this;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->set('ipAddress', $ipAddress);

        return $this;
    }

    public function setAuthTokenId(?string $authTokenId): self
    {
        $this->set('authTokenId', $authTokenId);

        return $this;
    }

    public function setAuthLogRecordId(?string $authLogRecordId): self
    {
        $this->set('authLogRecordId', $authLogRecordId);

        return $this;
    }

    public function setTarget(LinkParent $target): self
    {
        $this->set('targetId', $target->getId());
        $this->set('targetType', $target->getEntityType());

        return $this;
    }
}
