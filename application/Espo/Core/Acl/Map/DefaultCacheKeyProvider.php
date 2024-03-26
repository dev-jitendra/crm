<?php


namespace Espo\Core\Acl\Map;

use Espo\Entities\User;

class DefaultCacheKeyProvider implements CacheKeyProvider
{
    public function __construct(private User $user)
    {}

    public function get(): string
    {
        return 'aclMap/' . $this->user->getId();
    }
}
