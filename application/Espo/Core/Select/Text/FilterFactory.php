<?php


namespace Espo\Core\Select\Text;

use Espo\Core\Utils\Metadata;
use Espo\Core\InjectableFactory;
use Espo\Entities\User;
use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\Binding\ContextualBinder;

class FilterFactory
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    public function create(string $entityType, User $user): Filter
    {
        
        $className = $this->metadata->get(['selectDefs', $entityType, 'textFilterClassName']) ??
            DefaultFilter::class;

        $bindingContainer = BindingContainerBuilder::create()
            ->bindInstance(User::class, $user)
            ->inContext(
                $className,
                function (ContextualBinder $binder) use ($entityType) {
                    $binder->bindValue('$entityType', $entityType);
                }
            )
            ->inContext(
                DefaultFilter::class,
                function (ContextualBinder $binder) use ($entityType) {
                    $binder->bindValue('$entityType', $entityType);
                }
            )
            ->build();

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }
}
