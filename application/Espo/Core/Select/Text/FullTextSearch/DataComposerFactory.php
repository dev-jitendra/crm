<?php


namespace Espo\Core\Select\Text\FullTextSearch;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;

class DataComposerFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata
    ) {}

    public function create(string $entityType): DataComposer
    {
        $className = $this->getClassName($entityType);

        return $this->injectableFactory->createWith($className, [
            'entityType' => $entityType,
        ]);
    }

    
    private function getClassName(string $entityType): string
    {
        return
            $this->metadata->get([
                'selectDefs', $entityType, 'fullTextSearchDataComposerClassName'
            ]) ??
            DefaultDataComposer::class;
    }
}
