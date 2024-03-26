<?php


namespace Espo\Core\Select\AccessControl;

use Espo\Core\Portal\Acl;

class DefaultPortalFilterResolver implements FilterResolver
{
    public function __construct(private string $entityType, private Acl $acl)
    {}

    public function resolve(): ?string
    {
        if ($this->acl->checkReadNo($this->entityType)) {
            return 'no';
        }

        if ($this->acl->checkReadOnlyOwn($this->entityType)) {
            return 'portalOnlyOwn';
        }

        if ($this->acl->checkReadOnlyAccount($this->entityType)) {
            return 'portalOnlyAccount';
        }

        if ($this->acl->checkReadOnlyContact($this->entityType)) {
            return 'portalOnlyContact';
        }

        if ($this->acl->checkReadAll($this->entityType)) {
            return 'portalAll';
        }

        return 'no';
    }
}
