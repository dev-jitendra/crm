<?php


namespace Espo\Core\Select\AccessControl;

use Espo\Core\Acl;

class DefaultFilterResolver implements FilterResolver
{
    public function __construct(private string $entityType, private Acl $acl)
    {}

    public function resolve(): ?string
    {
        if ($this->acl->checkReadNo($this->entityType)) {
            return 'no';
        }

        if ($this->acl->checkReadOnlyOwn($this->entityType)) {
            return 'onlyOwn';
        }

        if ($this->acl->checkReadOnlyTeam($this->entityType)) {
            return 'onlyTeam';
        }

        if ($this->acl->checkReadAll($this->entityType)) {
            return 'all';
        }

        return 'no';
    }
}
