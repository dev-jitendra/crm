<?php


namespace Espo\Core\Portal\Acl\AccessChecker;

use Espo\Core\Acl\AccessChecker;
use Espo\Core\Acl\Exceptions\NotImplemented;
use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;
use Espo\Core\Portal\Acl\DefaultAccessChecker;
use Espo\Core\Portal\AclManager as PortalAclManager;
use Espo\Core\Utils\ClassFinder;
use Espo\Core\Utils\Metadata;

class AccessCheckerFactory
{
    
    private $defaultClassName = DefaultAccessChecker::class;

    public function __construct(
        private ClassFinder $classFinder,
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function create(string $scope, PortalAclManager $aclManager): AccessChecker
    {
        $className = $this->getClassName($scope);

        $bindingContainer = $this->createBindingContainer($aclManager);

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }

    
    private function getClassName(string $scope): string
    {
        
        $className1 = $this->metadata->get(['aclDefs', $scope, 'portalAccessCheckerClassName']);

        if ($className1) {
            return $className1;
        }

        if (!$this->metadata->get(['scopes', $scope])) {
            throw new NotImplemented();
        }

        
        
        $className2 = $this->classFinder->find('AclPortal', $scope);

        if ($className2) {
            return $className2;
        }

        return $this->defaultClassName;
    }

    private function createBindingContainer(PortalAclManager $aclManager): BindingContainer
    {
        $bindingData = new BindingData();
        $binder = new Binder($bindingData);

        $binder->bindInstance(PortalAclManager::class, $aclManager);

        return new BindingContainer($bindingData);
    }
}
