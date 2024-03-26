<?php


namespace Espo\Core\Acl\Table;

use Espo\Entities\User;

class DefaultCacheKeyProvider implements CacheKeyProvider
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function get(): string
    {
        return 'acl/' . $this->user->getId();
    }
}
