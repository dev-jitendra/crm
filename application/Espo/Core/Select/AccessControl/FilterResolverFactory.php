<?php


namespace Espo\Core\Select\AccessControl;

use Espo\Core\Exceptions\Error;
use Espo\Core\InjectableFactory;
use Espo\Core\Acl;
use Espo\Core\Portal\Acl as PortalAcl;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Acl\UserAclManagerProvider;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingData;

use Espo\Entities\User;
use RuntimeException;

class FilterResolverFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata,
        private UserAclManagerProvider $userAclManagerProvider
    ) {}

    public function create(string $entityType, User $user): FilterResolver
    {
        $className = !$user->isPortal() ?
            $this->getClassName($entityType) :
            $this->getPortalClassName($entityType);

        try {
            $acl = $this->userAclManagerProvider
                ->get($user)
                ->createUserAcl($user);
        }
        catch (Error $e) {
            throw new RuntimeException($e->getMessage());
        }

        $bindingData = new BindingData();

        $binder = new Binder($bindingData);
        $binder
            ->bindInstance(User::class, $user)
            ->bindInstance(Acl::class, $acl);

        if ($user->isPortal()) {
            $binder->bindInstance(PortalAcl::class, $acl);
        }

        $binder
            ->for($className)
            ->bindValue('$entityType', $entityType);

        $bindingContainer = new BindingContainer($bindingData);

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }

    
    private function getClassName(string $entityType): string
    {
        
        return $this->metadata->get(['selectDefs', $entityType, 'accessControlFilterResolverClassName']) ??
            DefaultFilterResolver::class;
    }

    
    private function getPortalClassName(string $entityType): string
    {
        
        return $this->metadata->get(['selectDefs', $entityType, 'portalAccessControlFilterResolverClassName']) ??
            DefaultPortalFilterResolver::class;
    }
}
