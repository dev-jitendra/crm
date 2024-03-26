<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\Field\DateTimeOptional;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\ORM\Entity;

class Task extends Entity
{
    public const ENTITY_TYPE = 'Task';

    public const STATUS_NOT_STARTED = 'Not Started';
    public const STATUS_STARTED = 'Started';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_CANCELED = 'Canceled';
    public const STATUS_DEFERRED = 'Deferred';

    public function getStatus(): ?string
    {
        return $this->get('status');
    }

    public function getDateStart(): ?DateTimeOptional
    {
        
        return $this->getValueObject('dateStart');
    }

    public function setDateStart(?DateTimeOptional $dateStart): void
    {
        $this->setValueObject('dateStart', $dateStart);
    }

    public function getDateEnd(): ?DateTimeOptional
    {
        
        return $this->getValueObject('dateEnd');
    }

    public function setDateEnd(?DateTimeOptional $dateEnd): void
    {
        $this->setValueObject('dateEnd', $dateEnd);
    }

    public function getAssignedUser(): ?Link
    {
        
        return $this->getValueObject('assignedUser');
    }

    public function getTeams(): LinkMultiple
    {
        
        return $this->getValueObject('teams');
    }

    
    public function getAttachmentIdList(): array
    {
        
        return $this->getLinkMultipleIdList('attachments');
    }
}
