<?php


namespace Espo\Core\Acl\AssignmentChecker;

use Espo\Core\Acl\AssignmentChecker;
use Espo\Core\Acl\DefaultAssignmentChecker;
use Espo\Core\Acl\Exceptions\NotImplemented;
use Espo\Core\InjectableFactory;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;

class AssignmentCheckerFactory
{
    
    private string $defaultClassName = DefaultAssignmentChecker::class;

    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function create(string $scope): AssignmentChecker
    {
        $className = $this->getClassName($scope);

        return $this->injectableFactory->create($className);
    }

    
    private function getClassName(string $scope): string
    {
        
        $className = $this->metadata->get(['aclDefs', $scope, 'assignmentCheckerClassName']);

        if ($className) {
            return $className;
        }

        if (!$this->metadata->get(['scopes', $scope])) {
            throw new NotImplemented();
        }

        
        return $this->defaultClassName;
    }
}
