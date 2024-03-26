<?php


namespace Espo\Core\Job;

use Espo\Core\Exceptions\Error;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\ClassFinder;

class JobFactory
{
    public function __construct(
        private ClassFinder $classFinder,
        private InjectableFactory $injectableFactory,
        private MetadataProvider $metadataProvider
    ) {}

    
    public function create(string $name): object
    {
        $className = $this->getClassName($name);

        if (!$className) {
            throw new Error("Job '{$name}' not found.");
        }

        return $this->createByClassName($className);
    }

    
    public function createByClassName(string $className): object
    {
        $job = $this->injectableFactory->create($className);

        return $job;
    }

    
    private function getClassName(string $name): ?string
    {
        
        $className = $this->metadataProvider->getJobClassName($name);

        if ($className) {
            return $className;
        }

        
        return $this->classFinder->find('Jobs', ucfirst($name));
    }
}
