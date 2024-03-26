<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;

class CaseObj extends \Espo\Core\ORM\Entity
{
    public const ENTITY_TYPE = 'Case';

    public const STATUS_NEW = 'New';
    public const STATUS_ASSIGNED = 'Assigned';
    public const STATUS_CLOSED = 'Closed';
    public const STATUS_PENDING = 'Pending';
    public const STATUS_REJECTED = 'Rejected';
    public const STATUS_DUPLICATE = 'Duplicate';

    protected $entityType = 'Case';

    public function getName(): ?string
    {
        return $this->get('name');
    }

    public function getStatus(): ?string
    {
        return $this->get('status');
    }

    public function getInboundEmailId(): ?string
    {
        return $this->get('inboundEmailId');
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

    public function getLead(): ?Link
    {
        
        return $this->getValueObject('lead');
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
