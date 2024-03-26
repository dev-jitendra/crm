<?php


namespace Espo\Tools\Pdf\Data;

use Espo\ORM\Entity;

use Espo\Core\Utils\Metadata;
use Espo\Core\InjectableFactory;

use Espo\Tools\Pdf\Data;
use Espo\Tools\Pdf\Params;

class DataLoaderManager
{
    private Metadata $metadata;
    private InjectableFactory $injectableFactory;

    public function __construct(Metadata $metadata, InjectableFactory $injectableFactory)
    {
        $this->metadata = $metadata;
        $this->injectableFactory = $injectableFactory;
    }

    public function load(Entity $entity, ?Params $params = null, ?Data $data = null): Data
    {
        if (!$params) {
            $params = Params::create();
        }

        if (!$data) {
            $data = Data::create();
        }

        
        $classNameList = $this->metadata->get(['pdfDefs', $entity->getEntityType(), 'dataLoaderClassNameList']) ?? [];

        foreach ($classNameList as $className) {
            $loader = $this->createLoader($className);

            $loadedData = $loader->load($entity, $params);

            $data = $data->withAdditionalTemplateData($loadedData);
        }

        return $data;
    }

    
    private function createLoader(string $className): DataLoader
    {
        return $this->injectableFactory->create($className);
    }
}
