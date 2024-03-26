<?php


namespace Espo\Core\Portal\Container;

use Espo\Core\Container\ContainerConfiguration as BaseContainerConfiguration;

class ContainerConfiguration extends BaseContainerConfiguration
{
    
    public function getLoaderClassName(string $name): ?string
    {
        $className = null;

        try {
            $className = $this->metadata->get(['app', 'portalContainerServices', $name, 'loaderClassName']);
        }
        catch (\Exception) {}

        if ($className && class_exists($className)) {
            return $className;
        }

        $className = 'Espo\Custom\Core\Portal\Loaders\\' . ucfirst($name);
        if (!class_exists($className)) {
            $className = 'Espo\Core\Portal\Loaders\\' . ucfirst($name);
        }

        if (class_exists($className)) {
            return $className;
        }

        return parent::getLoaderClassName($name);
    }

    
    public function getServiceClassName(string $name): ?string
    {
        return $this->metadata->get(['app', 'portalContainerServices', $name, 'className']) ??
            parent::getServiceClassName($name);
    }

    
    public function getServiceDependencyList(string $name): ?array
    {
        return
            $this->metadata->get(['app', 'portalContainerServices', $name, 'dependencyList']) ??
            parent::getServiceDependencyList($name);
    }

    public function isSettable(string $name): bool
    {
        return
            $this->metadata->get(['app', 'portalContainerServices', $name, 'settable']) ??
            parent::isSettable($name);
    }
}
