<?php


namespace Espo\Core\MassAction;

use Espo\Entities\User;

use Espo\Core\Exceptions\NotFound;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Utils\Metadata;
use Espo\Core\InjectableFactory;
use Espo\Core\AclManager;
use Espo\Core\Acl;
use Espo\Core\Binding\BindingContainerBuilder;

class MassActionFactory
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory,
        private AclManager $aclManager
    ) {}

    
    public function create(string $action, string $entityType): MassAction
    {
        $className = $this->getClassName($action, $entityType);

        if (!$className) {
            throw new NotFound("Mass action '{$action}' not found.");
        }

        if ($this->isDisabled($action, $entityType)) {
            throw new Forbidden("Mass action '{$action}' is disabled for '{$entityType}'.");
        }

        return $this->injectableFactory->create($className);
    }

    public function createForUser(string $action, string $entityType, User $user): MassAction
    {
        $className = $this->getClassName($action, $entityType);

        if (!$className) {
            throw new NotFound("Mass action '{$action}' not found.");
        }

        $bindingContainer = BindingContainerBuilder::create()
            ->bindInstance(User::class, $user)
            ->bindInstance(Acl::class, $this->aclManager->createUserAcl($user))
            ->build();

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }

    
    public function createWith(string $action, string $entityType, array $with): MassAction
    {
        $className = $this->getClassName($action, $entityType);

        if (!$className) {
            throw new NotFound("Mass action '{$action}' not found.");
        }

        return $this->injectableFactory->createWith($className, $with);
    }

    
    private function getClassName(string $action, string $entityType): ?string
    {
        
        $className = $this->getEntityTypeClassName($action, $entityType);

        if ($className) {
            return $className;
        }

        
        return $this->metadata->get(
            ['app', 'massActions', $action, 'implementationClassName']
        );
    }

    
    private function getEntityTypeClassName(string $action, string $entityType): ?string
    {
        
        return $this->metadata->get(
            ['recordDefs', $entityType, 'massActions', $action, 'implementationClassName']
        );
    }

    private function isDisabled(string $action, string $entityType): bool
    {
        $actionsDisabled = $this->metadata
            ->get(['recordDefs', $entityType, 'actionsDisabled']) ?? false;

        if ($actionsDisabled) {
            return true;
        }

        $massActionsDisabled = $this->metadata
            ->get(['recordDefs', $entityType, 'massActionsDisabled']) ?? false;

        if ($massActionsDisabled) {
            return true;
        }

        if ($this->needsToBeAllowed($entityType)) {
            if (!$this->isAllowed($action, $entityType)) {
                return true;
            }
        }

        return $this->metadata
            ->get(['recordDefs', $entityType, 'massActions', $action, 'disabled']) ?? false;
    }

    private function needsToBeAllowed(string $entityType): bool
    {
        $isObject = $this->metadata->get(['scopes', $entityType, 'object']) ?? false;

        if (!$isObject) {
            return true;
        }

        return $this->metadata
            ->get(['recordDefs', $entityType, 'notAllowedActionsDisabled']) ?? false;
    }

    private function isAllowed(string $action, string $entityType): bool
    {
        return $this->metadata
            ->get(['recordDefs', $entityType, 'massActions', $action, 'allowed']) ?? false;
    }
}
