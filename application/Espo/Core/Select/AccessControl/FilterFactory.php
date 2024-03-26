<?php


namespace Espo\Core\Select\AccessControl;

use Espo\Core\Acl;
use Espo\Core\AclManager;
use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;
use Espo\Core\Select\Helpers\FieldHelper;
use Espo\Core\Utils\Metadata;

use Espo\Entities\User;

use RuntimeException;

class FilterFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata,
        private AclManager $aclManager
    ) {}

    public function create(string $entityType, User $user, string $name): Filter
    {
        $className = $this->getClassName($entityType, $name);

        if (!$className) {
            throw new RuntimeException("Access control filter '{$name}' for '{$entityType}' does not exist.");
        }

        $bindingData = new BindingData();

        $binder = new Binder($bindingData);
        $binder
            ->bindInstance(User::class, $user)
            ->bindInstance(Acl::class, $this->aclManager->createUserAcl($user));
        $binder
            ->for($className)
            ->bindValue('$entityType', $entityType);
        $binder
            ->for(FieldHelper::class)
            ->bindValue('$entityType', $entityType);

        $bindingContainer = new BindingContainer($bindingData);

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }

    public function has(string $entityType, string $name): bool
    {
        return (bool) $this->getClassName($entityType, $name);
    }

    
    private function getClassName(string $entityType, string $name): ?string
    {
        if (!$name) {
            throw new RuntimeException("Empty access control filter name.");
        }

        
        $className = $this->metadata->get(
            [
                'selectDefs',
                $entityType,
                'accessControlFilterClassNameMap',
                $name,
            ]
        );

        if ($className) {
            return $className;
        }

        return $this->getDefaultClassName($name);
    }

    
    private function getDefaultClassName(string $name): ?string
    {
        $className = 'Espo\\Core\\Select\\AccessControl\\Filters\\' . ucfirst($name);

        if (!class_exists($className)) {
            return null;
        }

        
        return $className;
    }
}
