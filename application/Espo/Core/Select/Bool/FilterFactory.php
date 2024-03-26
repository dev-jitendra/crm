<?php


namespace Espo\Core\Select\Bool;

use Espo\Core\Exceptions\Error;

use Espo\Core\Select\Bool\Filter;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingData;

use Espo\Entities\User;

class FilterFactory
{
    private $injectableFactory;

    private $metadata;

    public function __construct(InjectableFactory $injectableFactory, Metadata $metadata)
    {
        $this->injectableFactory = $injectableFactory;
        $this->metadata = $metadata;
    }

    public function create(string $entityType, User $user, string $name): Filter
    {
        $className = $this->getClassName($entityType, $name);

        if (!$className) {
            throw new Error("Bool filter '{$name}' for '{$entityType}' does not exist.");
        }

        $bindingData = new BindingData();

        $binder = new Binder($bindingData);

        $binder
            ->bindInstance(User::class, $user)
            ->for($className)
            ->bindValue('$entityType', $entityType);

        $bindingContainer = new BindingContainer($bindingData);

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }

    public function has(string $entityType, string $name): bool
    {
        return (bool) $this->getClassName($entityType, $name);
    }

    
    protected function getClassName(string $entityType, string $name): ?string
    {
        if (!$name) {
            throw new Error("Empty bool filter name.");
        }

        $className = $this->metadata->get(
            [
                'selectDefs',
                $entityType,
                'boolFilterClassNameMap',
                $name,
            ]
        );

        if ($className) {
            
            return $className;
        }

        return $this->getDefaultClassName($name);
    }

    
    protected function getDefaultClassName(string $name): ?string
    {
        $className1 = $this->metadata->get(['app', 'select', 'boolFilterClassNameMap', $name]);

        if ($className1) {
            
            return $className1;
        }

        $className = 'Espo\\Core\\Select\\Bool\\Filters\\' . ucfirst($name);

        if (!class_exists($className)) {
            return null;
        }

        
        return $className;
    }
}
