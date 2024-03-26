<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\ORM\Entity;

class Reminder extends Entity
{
    public const ENTITY_TYPE = 'Reminder';

    public const TYPE_POPUP = 'Popup';
    public const TYPE_EMAIL = 'Email';

    public function getUserId(): ?string
    {
        return $this->get('userId');
    }

    public function getTargetEntityId(): ?string
    {
        return $this->get('entityId');
    }

    public function getTargetEntityType(): ?string
    {
        return $this->get('entityType');
    }

    public function getType(): ?string
    {
        return $this->get('type');
    }

    public function getSeconds(): int
    {
        return (int) $this->get('seconds');
    }
}
