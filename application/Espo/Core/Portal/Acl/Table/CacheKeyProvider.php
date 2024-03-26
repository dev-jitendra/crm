<?php


namespace Espo\Core\Portal\Acl\Table;

use Espo\Entities\Portal;
use Espo\Entities\User;

use Espo\Core\Acl\Table\CacheKeyProvider as CacheKeyProviderInterface;

class CacheKeyProvider implements CacheKeyProviderInterface
{
    public function __construct(private User $user, private Portal $portal)
    {}

    public function get(): string
    {
        return 'aclPortal/' . $this->portal->getId() . '/' . $this->user->getId();
    }
}
