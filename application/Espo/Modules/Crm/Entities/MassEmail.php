<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\ORM\Entity;

class MassEmail extends Entity
{
    public const ENTITY_TYPE = 'MassEmail';

    public const STATUS_COMPLETE = 'Complete';
    public const STATUS_FAILED = 'Failed';
    public const STATUS_IN_PROCESS = 'In Process';
    public const STATUS_PENDING = 'Pending';
    public const STATUS_DRAFT = 'Draft';

    public function getStatus(): ?string
    {
        return $this->get('status');
    }

    public function getEmailTemplateId(): ?string
    {
        return $this->get('emailTemplateId');
    }

    public function getCampaignId(): ?string
    {
        return $this->get('campaignId');
    }

    public function getInboundEmailId(): ?string
    {
        return $this->get('inboundEmailId');
    }

    public function getFromName(): ?string
    {
        return $this->get('fromName');
    }

    public function getReplyToName(): ?string
    {
        return $this->get('replyToName');
    }

    public function getFromAddress(): ?string
    {
        return $this->get('fromAddress');
    }

    public function getReplyToAddress(): ?string
    {
        return $this->get('replyToAddress');
    }

    public function storeSentEmails(): bool
    {
        return (bool) $this->get('storeSentEmails');
    }

    public function optOutEntirely(): bool
    {
        return (bool) $this->get('optOutEntirely');
    }
}
