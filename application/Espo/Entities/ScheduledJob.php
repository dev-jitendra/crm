<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;

class ScheduledJob extends Entity
{
    public const ENTITY_TYPE = 'ScheduledJob';

    public const STATUS_ACTIVE = 'Active';

    public function getName(): ?string
    {
        return $this->get('name');
    }

    public function getScheduling(): ?string
    {
        return $this->get('scheduling');
    }

    public function getJob(): ?string
    {
        return $this->get('job');
    }
}
