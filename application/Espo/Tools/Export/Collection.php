<?php


namespace Espo\Tools\Export;

use Espo\Core\FieldProcessing\ListLoadProcessor;
use Espo\Core\FieldProcessing\Loader\Params as LoaderParams;
use Espo\Core\Record\Service as RecordService;
use Espo\ORM\Collection as OrmCollection;
use Espo\ORM\Entity;
use Espo\Tools\Export\Processor\Params as ProcessorParams;
use IteratorAggregate;
use Traversable;


class Collection implements IteratorAggregate
{
    
    public function __construct(
        private OrmCollection $collection,
        private ListLoadProcessor $listLoadProcessor,
        private LoaderParams $loaderParams,
        private ?AdditionalFieldsLoader $additionalFieldsLoader,
        private RecordService $recordService,
        private ProcessorParams $processorParams
    ) {}

    public function getIterator(): Traversable
    {
        return (function () {
            foreach ($this->collection as $entity) {
                $this->prepareEntity($entity);

                yield $entity;
            }
        })();
    }

    private function prepareEntity(Entity $entity): void
    {
        $this->listLoadProcessor->process($entity, $this->loaderParams);

        
        if (method_exists($this->recordService, 'loadAdditionalFieldsForExport')) {
            $this->recordService->loadAdditionalFieldsForExport($entity);
        }

        if ($this->additionalFieldsLoader && $this->processorParams->getFieldList()) {
            $this->additionalFieldsLoader->load($entity, $this->processorParams->getFieldList());
        }

        foreach ($entity->getAttributeList() as $attribute) {
            $this->prepareEntityValue($entity, $attribute);
        }
    }

    private function prepareEntityValue(Entity $entity, string $attribute): void
    {
        if (!in_array($attribute, $this->processorParams->getAttributeList())) {
            $entity->clear($attribute);
        }
    }
}
