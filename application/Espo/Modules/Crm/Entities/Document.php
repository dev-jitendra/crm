<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\ORM\Entity;

class Document extends Entity
{
    public const ENTITY_TYPE = 'Document';

    public const STATUS_ACTIVE = 'Active';
    public const STATUS_DRAFT = 'Draft';

    public function getName(): ?string
    {
        return $this->get('name');
    }

    public function getFileId(): ?string
    {
        return $this->get('fileId');
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
