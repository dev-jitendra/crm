<?php


namespace Espo\Core\Acl\AccessChecker;

use Espo\Core\Acl\AccessChecker;
use Espo\Core\Acl\DefaultAccessChecker;
use Espo\Core\Acl\Exceptions\NotImplemented;
use Espo\Core\AclManager;
use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\ClassFinder;
use Espo\Core\Utils\Metadata;

class AccessCheckerFactory
{
    
    private string $defaultClassName = DefaultAccessChecker::class;

    public function __construct(
        private ClassFinder $classFinder,
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function create(string $scope, AclManager $aclManager): AccessChecker
    {
        $className = $this->getClassName($scope);

        $bindingContainer = $this->createBindingContainer($aclManager);

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }

    
    private function getClassName(string $scope): string
    {
        
        $className1 = $this->metadata->get(['aclDefs', $scope, 'accessCheckerClassName']);

        if ($className1) {
            return $className1;
        }

        if (!$this->metadata->get(['scopes', $scope])) {
            throw new NotImplemented("Access checker is not implemented for '{$scope}'.");
        }

        
        $className2 = $this->classFinder->find('Acl', $scope);

        if ($className2) {
            
            return $className2;
        }

        return $this->defaultClassName;
    }

    private function createBindingContainer(AclManager $aclManager): BindingContainer
    {
        $bindingData = new BindingData();

        $binder = new Binder($bindingData);
        $binder->bindInstance(AclManager::class, $aclManager);

        return new BindingContainer($bindingData);
    }
}
