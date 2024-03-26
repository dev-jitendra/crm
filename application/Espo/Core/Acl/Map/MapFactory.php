<?php


namespace Espo\Core\Acl\Map;

use Espo\Entities\User;

use Espo\Core\Acl\Table;
use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;

class MapFactory
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function create(User $user, Table $table): Map
    {
        $bindingContainer = $this->createBindingContainer($user, $table);

        return $this->injectableFactory->createWithBinding(Map::class, $bindingContainer);
    }

    private function createBindingContainer(User $user, Table $table): BindingContainer
    {
        $bindingData = new BindingData();

        $binder = new Binder($bindingData);
        $binder
            ->bindInstance(User::class, $user)
            ->bindInstance(Table::class, $table)
            ->bindImplementation(CacheKeyProvider::class, DefaultCacheKeyProvider::class);

        return new BindingContainer($bindingData);
    }
}
