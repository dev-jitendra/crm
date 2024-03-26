<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\Field\DateTime;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\ORM\Entity;

class Call extends Entity
{
    public const ENTITY_TYPE = 'Call';

    public const STATUS_PLANNED = 'Planned';
    public const STATUS_HELD = 'Held';
    public const STATUS_NOT_HELD = 'Not Held';

    public function getName(): ?string
    {
        return $this->get('name');
    }

    public function getStatus(): ?string
    {
        return $this->get('status');
    }

    public function getDateStart(): ?DateTime
    {
        
        return $this->getValueObject('dateStart');
    }

    public function setDateStart(?DateTime $dateStart): self
    {
        $this->setValueObject('dateStart', $dateStart);

        return $this;
    }

    public function getDateEnd(): ?DateTime
    {
        
        return $this->getValueObject('dateEnd');
    }

    public function setDateEnd(?DateTime $dateEnd): self
    {
        $this->setValueObject('dateEnd', $dateEnd);

        return $this;
    }

    public function setAssignedUserId(?string $assignedUserId): self
    {
        $this->set('assignedUserId', $assignedUserId);

        return $this;
    }

    public function getCreatedBy(): ?Link
    {
        
        return $this->getValueObject('createdBy');
    }

    public function getModifiedBy(): ?Link
    {
        
        return $this->getValueObject('modifiedBy');
    }

    public function getAssignedUser(): ?Link
    {
        
        return $this->getValueObject('assignedUser');
    }

    public function getTeams(): LinkMultiple
    {
        
        return $this->getValueObject('teams');
    }

    public function getUsers(): LinkMultiple
    {
        
        return $this->getValueObject('users');
    }

    public function getContacts(): LinkMultiple
    {
        
        return $this->getValueObject('contacts');
    }

    public function getLeads(): LinkMultiple
    {
        
        return $this->getValueObject('leads');
    }
}
