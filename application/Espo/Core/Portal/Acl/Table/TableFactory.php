<?php


namespace Espo\Core\Portal\Acl\Table;

use Espo\Entities\Portal;
use Espo\Entities\User;

use Espo\Core\Acl\Table\CacheKeyProvider;
use Espo\Core\Acl\Table\RoleListProvider;
use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;
use Espo\Core\Portal\Acl\Table;
use Espo\Core\Portal\Acl\Table\CacheKeyProvider as PortalCacheKeyProvider;
use Espo\Core\Portal\Acl\Table\RoleListProvider as PortalRoleListProvider;

class TableFactory
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    
    public function create(User $user, Portal $portal): Table
    {
        $bindingContainer = $this->createBindingContainer($user, $portal);

        return $this->injectableFactory->createWithBinding(Table::class, $bindingContainer);
    }

    private function createBindingContainer(User $user, Portal $portal): BindingContainer
    {
        $bindingData = new BindingData();

        $binder = new Binder($bindingData);

        $binder
            ->bindInstance(User::class, $user)
            ->bindInstance(Portal::class, $portal)
            ->bindImplementation(RoleListProvider::class, PortalRoleListProvider::class)
            ->bindImplementation(CacheKeyProvider::class, PortalCacheKeyProvider::class);

        return new BindingContainer($bindingData);
    }
}
