<?php


namespace Espo\Tools\Currency\Conversion;

use Espo\Core\Acl;
use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use Espo\Entities\User;
use Espo\ORM\Entity;

class EntityConverterFactory
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory,
        private User $user,
        private Acl $acl
    ) {}

    
    public function create(string $entityType): EntityConverter
    {
        
        $className = $this->metadata
            ->get(['app', 'currencyConversion', 'entityConverterClassNameMap', $entityType]) ??
            DefaultEntityConverter::class;

        $binding = BindingContainerBuilder::create()
            ->bindInstance(User::class, $this->user)
            ->bindInstance(Acl::class, $this->acl)
            ->build();

        return $this->injectableFactory->createWithBinding($className, $binding);
    }
}
