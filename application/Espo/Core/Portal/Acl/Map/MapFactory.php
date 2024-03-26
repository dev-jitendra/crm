<?php


namespace Espo\Core\Portal\Acl\Map;

use Espo\Entities\Portal;
use Espo\Entities\User;

use Espo\Core\Acl\Map\CacheKeyProvider;
use Espo\Core\Acl\Map\Map;
use Espo\Core\Acl\Map\MetadataProvider;
use Espo\Core\Acl\Table;
use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;
use Espo\Core\Portal\Acl\Map\CacheKeyProvider as PortalCacheKeyProvider;
use Espo\Core\Portal\Acl\Map\MetadataProvider as PortalMetadataProvider;
use Espo\Core\Portal\Acl\Table as PortalTable;

class MapFactory
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function create(User $user, PortalTable $table, Portal $portal): Map
    {
        $bindingContainer = $this->createBindingContainer($user, $table, $portal);

        return $this->injectableFactory->createWithBinding(Map::class, $bindingContainer);
    }

    private function createBindingContainer(User $user, PortalTable $table, Portal $portal): BindingContainer
    {
        $bindingData = new BindingData();

        $binder = new Binder($bindingData);

        $binder
            ->bindInstance(User::class, $user)
            ->bindInstance(Table::class, $table)
            ->bindInstance(Portal::class, $portal)
            ->bindImplementation(MetadataProvider::class, PortalMetadataProvider::class)
            ->bindImplementation(CacheKeyProvider::class, PortalCacheKeyProvider::class);

        return new BindingContainer($bindingData);
    }
}
