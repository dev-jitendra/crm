<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\Entities\Person;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;

class Contact extends Person
{
    public const ENTITY_TYPE = 'Contact';

    
    public function getAssignedUser(): ?Link
    {
        
        return $this->getValueObject('assignedUser');
    }

    
    public function getAccount(): ?Link
    {
        
        return $this->getValueObject('account');
    }

    
    public function getAccounts(): LinkMultiple
    {
        
        return $this->getValueObject('accounts');
    }

    
    public function getTeams(): LinkMultiple
    {
        
        return $this->getValueObject('teams');
    }

    
    public function getTitle(): ?string
    {
        return $this->get('title');
    }
}
