<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;

use Espo\Core\Field\Date;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;

use stdClass;

class EmailAccount extends Entity
{
    public const ENTITY_TYPE = 'EmailAccount';

    public const STATUS_ACTIVE = 'Active';

    public function isAvailableForFetching(): bool
    {
        return $this->isActive() && $this->get('useImap');
    }

    public function isAvailableForSending(): bool
    {
        return $this->isActive() && $this->get('useSmtp');
    }

    public function isActive(): bool
    {
        return $this->get('status') === self::STATUS_ACTIVE;
    }

    public function getEmailAddress(): ?string
    {
        return $this->get('emailAddress');
    }

    public function getAssignedUser(): ?Link
    {
        
        return $this->getValueObject('assignedUser');
    }

    public function getTeams(): LinkMultiple
    {
        
        return LinkMultiple::create();
    }

    public function keepFetchedEmailsUnread(): bool
    {
        return (bool) $this->get('keepFetchedEmailsUnread');
    }

    public function storeSentEmails(): bool
    {
        return (bool) $this->get('storeSentEmails');
    }

    public function getFetchData(): stdClass
    {
        $data = $this->get('fetchData') ?? (object) [];

        if (!property_exists($data, 'lastUID')) {
            $data->lastUID = (object) [];
        }

        if (!property_exists($data, 'lastDate')) {
            $data->lastDate = (object) [];
        }

        if (!property_exists($data, 'byDate')) {
            $data->byDate = (object) [];
        }

        return $data;
    }

    public function getFetchSince(): ?Date
    {
        
        return $this->getValueObject('fetchSince');
    }

    public function getEmailFolder(): ?Link
    {
        
        return $this->getValueObject('emailFolder');
    }

    
    public function getMonitoredFolderList(): array
    {
        return $this->get('monitoredFolders') ?? [];
    }

    public function getHost(): ?string
    {
        return $this->get('host');
    }

    public function getPort(): ?int
    {
        return $this->get('port');
    }

    public function getUsername(): ?string
    {
        return $this->get('username');
    }

    public function getPassword(): ?string
    {
        return $this->get('password');
    }

    public function getSecurity(): ?string
    {
        return $this->get('security');
    }

    
    public function getImapHandlerClassName(): ?string
    {
        
        return $this->get('imapHandler');
    }

    public function getSentFolder(): ?string
    {
        return $this->get('sentFolder');
    }

    public function getSmtpHost(): ?string
    {
        return $this->get('smtpHost');
    }

    public function getSmtpPort(): ?int
    {
        return $this->get('smtpPort');
    }

    public function getSmtpAuth(): bool
    {
        return $this->get('smtpAuth');
    }

    public function getSmtpSecurity(): ?string
    {
        return $this->get('smtpSecurity');
    }

    public function getSmtpUsername(): ?string
    {
        return $this->get('smtpUsername');
    }

    public function getSmtpPassword(): ?string
    {
        return $this->get('smtpPassword');
    }

    public function getSmtpAuthMechanism(): ?string
    {
        return $this->get('smtpAuthMechanism');
    }

    
    public function getSmtpHandlerClassName(): ?string
    {
        
        return $this->get('smtpHandler');
    }
}
