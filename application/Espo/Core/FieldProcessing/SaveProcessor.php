<?php


namespace Espo\Core\FieldProcessing;

use Espo\Core\ORM\Entity;
use Espo\Core\FieldProcessing\Saver\Params;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;


class SaveProcessor
{
    
    private $saverListMapCache = [];

    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata
    ) {}

    
    public function process(Entity $entity, array $options): void
    {
        $params = Params::create()->withRawOptions($options);

        foreach ($this->getSaverList($entity->getEntityType()) as $processor) {
            $processor->process($entity, $params);
        }
    }

    
    private function getSaverList(string $entityType): array
    {
        if (array_key_exists($entityType, $this->saverListMapCache)) {
            return $this->saverListMapCache[$entityType];
        }

        $list = [];

        foreach ($this->getSaverClassNameList($entityType) as $className) {
            $list[] = $this->createSaver($className);
        }

        $this->saverListMapCache[$entityType] = $list;

        return $list;
    }

    
    private function getSaverClassNameList(string $entityType): array
    {
        $list = $this->metadata
            ->get(['app', 'fieldProcessing', 'saverClassNameList']) ?? [];

        $additionalList = $this->metadata
            ->get(['recordDefs', $entityType, 'saverClassNameList']) ?? [];

        
        return array_merge($list, $additionalList);
    }

    
    private function createSaver(string $className): Saver
    {
        return $this->injectableFactory->create($className);
    }
}
