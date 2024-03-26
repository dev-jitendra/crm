<?php


namespace Espo\Core\Acl\Table;

use Espo\Entities\User;
use Espo\Core\Acl\Table;
use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;

class DefaultTableFactory implements TableFactory
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    
    public function create(User $user): Table
    {
        $bindingContainer = $this->createBindingContainer($user);

        return $this->injectableFactory->createWithBinding(DefaultTable::class, $bindingContainer);
    }

    private function createBindingContainer(User $user): BindingContainer
    {
        $bindingData = new BindingData();

        $binder = new Binder($bindingData);

        $binder
            ->bindInstance(User::class, $user)
            ->bindImplementation(RoleListProvider::class, DefaultRoleListProvider::class)
            ->bindImplementation(CacheKeyProvider::class, DefaultCacheKeyProvider::class);

        return new BindingContainer($bindingData);
    }
}
