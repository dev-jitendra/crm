<?php


namespace Espo\Core\Notification;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\ClassFinder;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;
use Espo\ORM\Repository\Util as RepositoryUtil;

class AssignmentNotificatorFactory
{
    
    protected string $defaultClassName = DefaultAssignmentNotificator::class;

    public function __construct(
        private InjectableFactory $injectableFactory,
        private ClassFinder $classFinder,
        private Metadata $metadata
    ) {}

    
    public function createByClass(string $className): AssignmentNotificator
    {
        $entityType = RepositoryUtil::getEntityTypeByClass($className);

        
        return $this->create($entityType);
    }

    
    public function create(string $entityType): object 
    {
        $className = $this->getClassName($entityType);

        return $this->injectableFactory->create($className);
    }

    
    private function getClassName(string $entityType): string
    {
        
        $className1 = $this->metadata->get(['notificationDefs', $entityType, 'assignmentNotificatorClassName']);

        if ($className1) {
            return $className1;
        }

        
        
        $className2 = $this->classFinder->find('Notificators', $entityType);

        if ($className2) {
            return $className2;
        }

        return $this->defaultClassName;
    }
}
