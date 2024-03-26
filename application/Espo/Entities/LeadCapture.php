<?php


namespace Espo\Entities;

class LeadCapture extends \Espo\Core\ORM\Entity
{
    public const ENTITY_TYPE = 'LeadCapture';

    
    public function isToSubscribeContactIfExists(): bool
    {
        return $this->get('subscribeToTargetList') && $this->get('subscribeContactToTargetList');
    }

    
    public function getFieldList(): array
    {
        return $this->get('fieldList') ?? [];
    }

    public function getOptInConfirmationSuccessMessage(): ?string
    {
        return $this->get('optInConfirmationSuccessMessage');
    }

    public function duplicateCheck(): bool
    {
        return (bool) $this->get('duplicateCheck');
    }

    public function skipOptInConfirmationIfSubscribed(): bool
    {
        return (bool) $this->get('skipOptInConfirmationIfSubscribed');
    }

    public function createLeadBeforeOptInConfirmation(): bool
    {
        return (bool) $this->get('createLeadBeforeOptInConfirmation');
    }

    public function optInConfirmation(): bool
    {
        return (bool) $this->get('optInConfirmation');
    }

    public function getOptInConfirmationLifetime(): ?int
    {
        return $this->get('optInConfirmationLifetime');
    }

    public function subscribeToTargetList(): bool
    {
        return (bool) $this->get('subscribeToTargetList');
    }

    public function subscribeContactToTargetList(): bool
    {
        return (bool) $this->get('subscribeContactToTargetList');
    }

    public function getApiKey(): ?string
    {
        return $this->get('apiKey');
    }

    public function getName(): ?string
    {
        return $this->get('name');
    }

    public function getTargetTeamId(): ?string
    {
        return $this->get('targetTeamId');
    }

    public function getTargetListId(): ?string
    {
        return $this->get('targetListId');
    }

    public function getCampaignId(): ?string
    {
        return $this->get('campaignId');
    }

    public function getInboundEmailId(): ?string
    {
        return $this->get('inboundEmailId');
    }

    public function getLeadSource(): ?string
    {
        return $this->get('leadSource');
    }

    public function getOptInConfirmationEmailTemplateId(): ?string
    {
        return $this->get('optInConfirmationEmailTemplateId');
    }

    
    public function getPhoneNumberCountry(): ?string
    {
        return $this->get('phoneNumberCountry');
    }
}
