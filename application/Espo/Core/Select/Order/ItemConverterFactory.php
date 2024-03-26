<?php


namespace Espo\Core\Select\Order;

use Espo\Entities\User;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\Binding\ContextualBinder;

use RuntimeException;

class ItemConverterFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata,
        private User $user
    ) {}

    public function has(string $entityType, string $field): bool
    {
        return (bool) $this->getClassName($entityType, $field);
    }

    public function create(string $entityType, string $field): ItemConverter
    {
        $className = $this->getClassName($entityType, $field);

        if (!$className) {
            throw new RuntimeException("Order item converter class name is not defined.");
        }

        $container = BindingContainerBuilder::create()
            ->bindInstance(User::class, $this->user)
            ->inContext($className, function (ContextualBinder $binder) use ($entityType) {
                $binder->bindValue('$entityType', $entityType);
            })
            ->build();

        return $this->injectableFactory->createWithBinding($className, $container);
    }

    
    private function getClassName(string $entityType, string $field): ?string
    {
        
        $className1 = $this->metadata->get([
            'selectDefs', $entityType, 'orderItemConverterClassNameMap', $field
        ]);

        if ($className1) {
            return $className1;
        }

        $type = $this->metadata->get([
            'entityDefs', $entityType, 'fields', $field, 'type'
        ]);

        if (!$type) {
            return null;
        }

        
        $className2 = $this->metadata->get([
            'app', 'select', 'orderItemConverterClassNameMap', $type
        ]);

        if ($className2) {
            return $className2;
        }

        $className3 = 'Espo\\Core\\Select\\Order\\ItemConverters\\' . ucfirst($type) . 'Type';

        if (class_exists($className3)) {
            
            return $className3;
        }

        return null;
    }
}
