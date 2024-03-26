<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\Field\Currency;
use Espo\Core\Field\Date;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\ORM\Entity;

class Opportunity extends Entity
{
    public const ENTITY_TYPE = 'Opportunity';

    public const STAGE_CLOSED_WON = 'Closed Won';
    public const STAGE_CLOSED_LOST = 'Closed Lost';

    public function getName(): ?string
    {
        return $this->get('name');
    }

    public function setName(?string $name): self
    {
        $this->set('name', $name);

        return $this;
    }

    public function getAmount(): ?Currency
    {
        
        return $this->getValueObject('amount');
    }

    public function setAmount(?Currency $amount): self
    {
        $this->setValueObject('amount', $amount);

        return $this;
    }

    public function getCloseDate(): ?Date
    {
        
        return $this->getValueObject('closeDate');
    }

    public function setCloseDate(?Date $closeDate): self
    {
        $this->setValueObject('closeDate', $closeDate);

        return $this;
    }

    public function getStage(): ?string
    {
        return $this->get('stage');
    }

    public function setStage(?string $stage): void
    {
        $this->set('stage', $stage);
    }

    public function getLastStage(): ?string
    {
        return $this->get('lastStage');
    }

    public function getProbability(): ?int
    {
        return $this->get('probability');
    }

    public function setProbability(?int $probability): void
    {
        $this->set('probability', $probability);
    }

    public function getAccount(): ?Link
    {
        
        return $this->getValueObject('account');
    }

    
    public function getContact(): ?Link
    {
        
        return $this->getValueObject('contact');
    }

    public function getContacts(): LinkMultiple
    {
        
        return $this->getValueObject('contacts');
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
