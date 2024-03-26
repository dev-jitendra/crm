<?php


namespace Espo\Core\Select\Primary;

use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use Espo\Entities\User;

use RuntimeException;

class FilterFactory
{
    public function __construct(private InjectableFactory $injectableFactory, private Metadata $metadata)
    {}

    public function create(string $entityType, User $user, string $name): Filter
    {
        $className = $this->getClassName($entityType, $name);

        if (!$className) {
            throw new RuntimeException("Primary filter '{$name}' for '{$entityType}' does not exist.");
        }

        $bindingData = new BindingData();

        $binder = new Binder($bindingData);

        $binder
            ->bindInstance(User::class, $user)
            ->for($className)
            ->bindValue('$entityType', $entityType)
            ->bindValue('$name', $name);

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
            throw new RuntimeException("Empty primary filter name.");
        }

        $className = $this->metadata->get(
            [
                'selectDefs',
                $entityType,
                'primaryFilterClassNameMap',
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
        $className = 'Espo\\Core\\Select\\Primary\\Filters\\' . ucfirst($name);

        if (!class_exists($className)) {
            return null;
        }

        
        return $className;
    }
}
