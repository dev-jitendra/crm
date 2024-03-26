<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\Field\Date;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\ORM\Entity;

class Campaign extends Entity
{
    public const ENTITY_TYPE = 'Campaign';

    public const TYPE_EMAIL = 'Email';
    public const TYPE_MAIL = 'Mail';

    public const TYPE_ACTIVE = 'Active';

    public function getName(): ?string
    {
        return $this->get('name');
    }

    public function getType(): ?string
    {
        return $this->get('type');
    }

    public function getStartDate(): ?Date
    {
        
        return $this->getValueObject('startDate');
    }

    public function getEndDate(): ?Date
    {
        
        return $this->getValueObject('endDate');
    }

    public function getAssignedUser(): ?Link
    {
        
        return $this->getValueObject('assignedUser');
    }

    public function getTeams(): LinkMultiple
    {
        
        return $this->getValueObject('teams');
    }
}
