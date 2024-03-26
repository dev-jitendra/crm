<?php


namespace Espo\Tools\Pdf;

use Espo\Core\Exceptions\Error;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Collection;
use Espo\ORM\Entity;

class PrinterController
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory,
        private Template $template,
        private string $engine
    ) {}

    
    public function printEntity(Entity $entity, ?Params $params, ?Data $data = null): Contents
    {
        $params = $params ?? new Params();
        $data = $data ?? new Data();

        return $this->createEntityPrinter()->print($this->template, $entity, $params,  $data);
    }

    
    public function printCollection(
        Collection $collection,
        ?Params $params,
        ?IdDataMap $idDataMap = null
    ): Contents {

        $params = $params ?? new Params();
        $idDataMap = $idDataMap ?? new IdDataMap();

        if ($this->hasCollectionPrinter()) {
            return $this->createCollectionPrinter()->print($this->template, $collection, $params, $idDataMap);
        }

        $printer = $this->createEntityPrinter();

        $zipper = new Zipper();

        foreach ($collection as $entity) {
            $data = $idDataMap->get($entity->getId()) ?? new Data();

            $itemContents = $printer->print($this->template, $entity, $params, $data);

            $zipper->add($itemContents, $entity->getId());
        }

        $zipper->archive();

        return new ZipContents($zipper->getFilePath());
    }

    
    private function createEntityPrinter(): EntityPrinter
    {
        
        $className = $this->metadata
            ->get(['app', 'pdfEngines', $this->engine, 'implementationClassNameMap', 'entity']) ?? null;

        if (!$className) {
            throw new Error("Unknown PDF engine '{$this->engine}', type 'entity'.");
        }

        return $this->injectableFactory->create($className);
    }

    
    private function createCollectionPrinter(): CollectionPrinter
    {
        $className = $this->getCollectionPrinterClassName();

        if (!$className) {
            throw new Error("Unknown PDF engine '{$this->engine}', type 'collection'.");
        }

        return $this->injectableFactory->create($className);
    }

    private function hasCollectionPrinter(): bool
    {
        return (bool) $this->getCollectionPrinterClassName();
    }

    
    private function getCollectionPrinterClassName(): ?string
    {
        
        return $this->metadata
            ->get(['app', 'pdfEngines', $this->engine, 'implementationClassNameMap', 'collection']) ?? null;
    }

}
