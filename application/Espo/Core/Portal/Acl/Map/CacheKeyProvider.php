<?php


namespace Espo\Core\Portal\Acl\Map;

use Espo\Entities\Portal;
use Espo\Entities\User;
use Espo\Core\Acl\Map\CacheKeyProvider as CacheKeyProviderInterface;

class CacheKeyProvider implements CacheKeyProviderInterface
{
    public function __construct(private User $user, private Portal $portal)
    {}

    public function get(): string
    {
        return 'aclPortalMap/' . $this->portal->getId() . '/' . $this->user->getId();
    }
}
