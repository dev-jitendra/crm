<?php


namespace Espo\Core\Job;

use Espo\Core\InjectableFactory;

use RuntimeException;

class PreparatorFactory
{
    public function __construct(
        private MetadataProvider $metadataProvider,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function create(string $name): Preparator
    {
        
        $className = $this->metadataProvider->getPreparatorClassName($name);

        if (!$className) {
            throw new RuntimeException("Preparator for job '{$name}' not found.");
        }

        return $this->injectableFactory->create($className);
    }
}
