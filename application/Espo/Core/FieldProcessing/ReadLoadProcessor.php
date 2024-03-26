<?php


namespace Espo\Core\FieldProcessing;

use Espo\ORM\Entity;

use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;


class ReadLoadProcessor
{
    
    private array $loaderListMapCache = [];

    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata)
    {}

    public function process(Entity $entity, ?Params $params = null): void
    {
        if (!$params) {
            $params = new Params();
        }

        foreach ($this->getLoaderList($entity->getEntityType()) as $processor) {
            $processor->process($entity, $params);
        }
    }

    
    private function getLoaderList(string $entityType): array
    {
        if (array_key_exists($entityType, $this->loaderListMapCache)) {
            return $this->loaderListMapCache[$entityType];
        }

        $list = [];

        foreach ($this->getLoaderClassNameList($entityType) as $className) {
            $list[] = $this->createLoader($className);
        }

        $this->loaderListMapCache[$entityType] = $list;

        return $list;
    }

    
    private function getLoaderClassNameList(string $entityType): array
    {
        $list = $this->metadata
            ->get(['app', 'fieldProcessing', 'readLoaderClassNameList']) ?? [];

        $additionalList = $this->metadata
            ->get(['recordDefs', $entityType, 'readLoaderClassNameList']) ?? [];

        
        return array_merge($list, $additionalList);
    }

    
    private function createLoader(string $className): Loader
    {
        return $this->injectableFactory->create($className);
    }
}
