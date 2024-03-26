<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;

class EmailTemplate extends Entity
{
    public const ENTITY_TYPE = 'EmailTemplate';

    public function getSubject(): ?string
    {
        return $this->get('subject');
    }

    public function getBody(): ?string
    {
        return $this->get('body');
    }

    public function isHtml(): bool
    {
        return (bool) $this->get('isHtml');
    }

    
    public function getAttachmentIdList(): array
    {
        
        return $this->getLinkMultipleIdList('attachments');
    }
}
