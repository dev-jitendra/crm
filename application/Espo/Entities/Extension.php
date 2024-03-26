<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;

class Extension extends Entity
{
    public const ENTITY_TYPE = 'Extension';

    public const LICENSE_STATUS_VALID = 'Valid';
    public const LICENSE_STATUS_INVALID = 'Invalid';
    public const LICENSE_STATUS_EXPIRED = 'Expired';
    public const LICENSE_STATUS_SOFT_EXPIRED = 'Soft-Expired';

    public function getName(): string
    {
        return (string) $this->get('name');
    }

    public function getVersion(): string
    {
        return (string) $this->get('version');
    }

    public function getLicenseStatusMessage(): ?string
    {
        return $this->get('licenseStatusMessage');
    }

    public function getLicenseStatus(): ?string
    {
        return $this->get('licenseStatus');
    }

    public function isInstalled(): bool
    {
        return (bool) $this->get('isInstalled');
    }
}
