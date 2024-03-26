<?php


namespace Espo\Core\Select\AccessControl\FilterResolvers;

use Espo\Core\Acl;
use Espo\Core\Select\AccessControl\FilterResolver;

class Boolean implements FilterResolver
{
    private string $entityType;

    private Acl $acl;

    public function __construct(string $entityType, Acl $acl)
    {
        $this->entityType = $entityType;
        $this->acl = $acl;
    }

    public function resolve(): ?string
    {
        if ($this->acl->checkScope($this->entityType)) {
            return 'all';
        }

        return 'no';
    }
}
