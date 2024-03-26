<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\Field\DateTime;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;

class Lead extends \Espo\Core\Entities\Person
{
    public const ENTITY_TYPE = 'Lead';

    public const STATUS_NEW = 'New';
    public const STATUS_ASSIGNED = 'Assigned';
    public const STATUS_IN_PROCESS = 'In Process';
    public const STATUS_CONVERTED = 'Converted';
    public const STATUS_RECYCLED = 'Recycled';
    public const STATUS_DEAD = 'Dead';

    public function getStatus(): ?string
    {
        return $this->get('status');
    }

    
    protected function _getName()
    {
        if (!$this->hasInContainer('name') || !$this->getFromContainer('name')) {
            if ($this->get('accountName')) {
                return $this->get('accountName');
            }

            if ($this->get('emailAddress')) {
                return $this->get('emailAddress');
            }

            if ($this->get('phoneNumber')) {
                return $this->get('phoneNumber');
            }
        }

        return $this->getFromContainer('name');
    }

    
    protected function _hasName()
    {
        if ($this->hasInContainer('name')) {
            return true;
        }

        if ($this->has('accountName')) {
            return true;
        }

        if ($this->has('emailAddress')) {
            return true;
        }

        if ($this->has('phoneNumber')) {
            return true;
        }

        return false;
    }

    public function getCampaign(): ?Link
    {
        
        return $this->getValueObject('campaign');
    }

    public function getAssignedUser(): ?Link
    {
        
        return $this->getValueObject('assignedUser');
    }

    public function getTeams(): LinkMultiple
    {
        
        return $this->getValueObject('teams');
    }

    public function getCreatedAccount(): ?Link
    {
        
        return $this->getValueObject('createdAccount');
    }

    public function getCreatedContact(): ?Link
    {
        
        return $this->getValueObject('createdContact');
    }

    public function getCreatedOpportunity(): ?Link
    {
        
        return $this->getValueObject('createdOpportunity');
    }

    public function getConvertedAt(): ?DateTime
    {
        
        return $this->getValueObject('convertedAt');
    }
}
