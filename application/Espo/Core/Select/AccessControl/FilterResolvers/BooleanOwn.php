<?php


namespace Espo\Core\Select\AccessControl\FilterResolvers;

use Espo\Core\Acl;
use Espo\Core\Select\AccessControl\FilterResolver;
use Espo\Entities\User;

class BooleanOwn implements FilterResolver
{
    private string $entityType;
    private Acl $acl;
    private User $user;

    public function __construct(string $entityType, Acl $acl, User $user)
    {
        $this->entityType = $entityType;
        $this->acl = $acl;
        $this->user = $user;
    }

    public function resolve(): ?string
    {
        if (!$this->acl->checkScope($this->entityType)) {
            return 'no';
        }

        if ($this->user->isAdmin()) {
            return 'all';
        }

        return 'onlyOwn';
    }
}
