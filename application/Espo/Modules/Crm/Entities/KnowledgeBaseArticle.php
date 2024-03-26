<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\ORM\Entity;

class KnowledgeBaseArticle extends Entity
{
    public const ENTITY_TYPE = 'KnowledgeBaseArticle';

    public const STATUS_PUBLISHED = 'Published';
    public const STATUS_ARCHIVED = 'Archived';

    public function getOrder(): ?int
    {
        return $this->get('order');
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
