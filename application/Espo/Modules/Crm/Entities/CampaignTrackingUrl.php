<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\ORM\Entity;

class CampaignTrackingUrl extends Entity
{
    public const ENTITY_TYPE = 'CampaignTrackingUrl';

    public const ACTION_SHOW_MESSAGE = 'Show Message';

    public function getCampaignId(): ?string
    {
        return $this->get('campaignId');
    }

    public function getAction(): ?string
    {
        return $this->get('action');
    }

    public function getMessage(): ?string
    {
        return $this->get('message');
    }

    public function getUrl(): ?string
    {
        return $this->get('url');
    }

    protected function _getUrlToUse(): string
    {
        return '{trackingUrl:' . $this->id . '}';
    }

    protected function _hasUrlToUse(): bool
    {
        return !$this->isNew();
    }
}
