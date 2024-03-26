<?php


namespace Espo\Core\Select\Where;

use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

use Espo\Entities\User;

class ConverterFactory
{
    public function __construct(private InjectableFactory $injectableFactory, private Metadata $metadata)
    {}

    public function create(string $entityType, User $user): Converter
    {
        $dateTimeItemTransformer = $this->createDateTimeItemTransformer($entityType, $user);

        $itemConverter = $this->createItemConverter($entityType, $user, $dateTimeItemTransformer);

        $className = $this->getConverterClassName($entityType);

        $bindingData = new BindingData();

        $binder = new Binder($bindingData);
        $binder
            ->bindInstance(User::class, $user);
        $binder
            ->for($className)
            ->bindValue('$entityType', $entityType)
            ->bindInstance(ItemConverter::class, $itemConverter);

        $bindingContainer = new BindingContainer($bindingData);

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }

    private function createDateTimeItemTransformer(string $entityType, User $user): DateTimeItemTransformer
    {
        $className = $this->getDateTimeItemTransformerClassName($entityType);

        $bindingData = new BindingData();

        $binder = new Binder($bindingData);

        $binder
            ->bindInstance(User::class, $user);

        $binder
            ->for($className)
            ->bindValue('$entityType', $entityType);

        $binder
            ->for(DateTimeItemTransformer::class)
            ->bindValue('$entityType', $entityType);

        $bindingContainer = new BindingContainer($bindingData);

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }

    private function createItemConverter(
        string $entityType,
        User $user,
        DateTimeItemTransformer $dateTimeItemTransformer
    ): ItemConverter {

        $className = $this->getItemConverterClassName($entityType);

        $bindingData = new BindingData();

        $binder = new Binder($bindingData);
        $binder
            ->bindInstance(User::class, $user);
        $binder
            ->for($className)
            ->bindValue('$entityType', $entityType)
            ->bindInstance(DateTimeItemTransformer::class, $dateTimeItemTransformer);
        $binder
            ->for(ItemGeneralConverter::class)
            ->bindValue('$entityType', $entityType)
            ->bindInstance(DateTimeItemTransformer::class, $dateTimeItemTransformer);

        $bindingContainer = new BindingContainer($bindingData);

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }

    
    private function getConverterClassName(string $entityType): string
    {
        $className = $this->metadata->get(['selectDefs', $entityType, 'whereConverterClassName']);

        if ($className) {
            return $className;
        }

        return Converter::class;
    }

    
    private function getItemConverterClassName(string $entityType): string
    {
        $className = $this->metadata->get(['selectDefs', $entityType, 'whereItemConverterClassName']);

        if ($className) {
            return $className;
        }

        return ItemGeneralConverter::class;
    }

    
    private function getDateTimeItemTransformerClassName(string $entityType): string
    {
        $className = $this->metadata
            ->get(['selectDefs', $entityType, 'whereDateTimeItemTransformerClassName']);

        if ($className) {
            return $className;
        }

        return DateTimeItemTransformer::class;
    }
}
