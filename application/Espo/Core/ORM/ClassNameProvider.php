<?php


namespace Espo\Core\ORM;

use Espo\Core\ORM\Entity as BaseEntity;
use Espo\Core\Repositories\Database as DatabaseRepository;
use Espo\Core\Utils\ClassFinder;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity as Entity;
use Espo\ORM\Repository\Repository as Repository;

class ClassNameProvider
{
    
    private const DEFAULT_ENTITY_CLASS_NAME = BaseEntity::class;
    
    private const DEFAULT_REPOSITORY_CLASS_NAME = DatabaseRepository::class;

    
    private array $entityCache = [];

    
    private array $repositoryCache = [];

    public function __construct(
        private Metadata $metadata,
        private ClassFinder $classFinder
    ) {}

    
    public function getEntityClassName(string $entityType): string
    {
        if (!array_key_exists($entityType, $this->entityCache)) {
            $this->entityCache[$entityType] = $this->findEntityClassName($entityType);
        }

        return $this->entityCache[$entityType];
    }

    
    public function getRepositoryClassName(string $entityType): string
    {
        if (!array_key_exists($entityType, $this->entityCache)) {
            $this->repositoryCache[$entityType] = $this->findRepositoryClassName($entityType);
        }

        return $this->repositoryCache[$entityType];
    }

    
    private function findEntityClassName(string $entityType): string
    {
        
        $className = $this->classFinder->find('Entities', $entityType);

        if ($className) {
            return $className;
        }

        
        $template = $this->metadata->get(['scopes', $entityType, 'type']);

        if ($template) {
            
            $className = $this->metadata->get(['app', 'entityTemplates', $template, 'entityClassName']);
        }

        if ($className) {
            return $className;
        }

        return self::DEFAULT_ENTITY_CLASS_NAME;
    }

    
    private function findRepositoryClassName(string $entityType): string
    {
        
        $className = $this->classFinder->find('Repositories', $entityType);

        if ($className) {
            return $className;
        }

        
        $template = $this->metadata->get(['scopes', $entityType, 'type']);

        if ($template) {
            
            $className = $this->metadata->get(['app', 'entityTemplates', $template, 'repositoryClassName']);
        }

        if ($className) {
            return $className;
        }

        return self::DEFAULT_REPOSITORY_CLASS_NAME;
    }
}
