<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\Field\DateTimeOptional;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\ORM\Entity;

class Meeting extends Entity
{
    public const ENTITY_TYPE = 'Meeting';

    public const ATTENDEE_STATUS_NONE = 'None';
    public const ATTENDEE_STATUS_ACCEPTED = 'Accepted';
    public const ATTENDEE_STATUS_TENTATIVE = 'Tentative';
    public const ATTENDEE_STATUS_DECLINED = 'Declined';

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

    public function getDateStart(): ?DateTimeOptional
    {
        
        return $this->getValueObject('dateStart');
    }

    public function setDateStart(?DateTimeOptional $dateStart): self
    {
        $this->setValueObject('dateStart', $dateStart);

        return $this;
    }

    public function getDateEnd(): ?DateTimeOptional
    {
        
        return $this->getValueObject('dateEnd');
    }

    public function setDateEnd(?DateTimeOptional $dateEnd): self
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
