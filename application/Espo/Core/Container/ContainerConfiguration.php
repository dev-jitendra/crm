<?php


namespace Espo\Core\Container;

use Espo\Core\Utils\Log;
use Espo\Core\Utils\Metadata;

use ReflectionClass;
use Exception;

class ContainerConfiguration implements Configuration
{
    
    private Log $log;

    
    protected Metadata $metadata;

    public function __construct(Log $log, Metadata $metadata)
    {
        $this->log = $log;
        $this->metadata = $metadata;
    }

    
    public function getLoaderClassName(string $name): ?string
    {
        $className = null;

        try {
            $className = $this->metadata->get(['app', 'containerServices', $name, 'loaderClassName']);

            if (!$className) {
                
                
                $className = $this->metadata->get(['app', 'loaders', ucfirst($name)]);
            }
        } catch (Exception) {}

        if ($className && class_exists($className)) {
            return $className;
        }

        $className = 'Espo\Custom\Core\Loaders\\' . ucfirst($name);

        if (!class_exists($className)) {
            $className = 'Espo\Core\Loaders\\' . ucfirst($name);
        }

        if (class_exists($className)) {
            $class = new ReflectionClass($className);

            if ($class->isInstantiable()) {
                return $className;
            }
        }

        return null;
    }

    
    public function getServiceClassName(string $name): ?string
    {
        return $this->metadata->get(['app', 'containerServices', $name, 'className']) ?? null;
    }

    
    public function getServiceDependencyList(string $name): ?array
    {
        return $this->metadata->get(['app', 'containerServices', $name, 'dependencyList']) ?? null;
    }

    public function isSettable(string $name): bool
    {
        return $this->metadata->get(['app', 'containerServices', $name, 'settable']) ?? false;
    }
}
