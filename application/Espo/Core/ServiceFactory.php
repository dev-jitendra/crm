<?php


namespace Espo\Core;

use Espo\Core\Utils\ClassFinder;

use RuntimeException;


class ServiceFactory
{
    private $classFinder;
    private $injectableFactory;

    public function __construct(ClassFinder $classFinder, InjectableFactory $injectableFactory)
    {
        $this->classFinder = $classFinder;
        $this->injectableFactory = $injectableFactory;
    }

    
    private function getClassName(string $name): ?string
    {
        return $this->classFinder->find('Services', $name);
    }

    public function checkExists(string $name): bool
    {
        $className = $this->getClassName($name);

        if (!$className) {
            return false;
        }

        return true;
    }

    
    public function createWith(string $name, array $with): object
    {
        $className = $this->getClassName($name);

        if (!$className) {
            throw new RuntimeException("Service '{$name}' was not found.");
        }

        $obj = $this->injectableFactory->createWith($className, $with);

        
        if (method_exists($obj, 'prepare')) {
            $obj->prepare();
        }

        return $obj;
    }

    public function create(string $name): object
    {
        return $this->createWith($name, []);
    }
}
