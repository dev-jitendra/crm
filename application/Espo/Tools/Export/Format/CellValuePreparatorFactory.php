<?php


namespace Espo\Tools\Export\Format;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use Espo\Tools\Export\Format\Xlsx\CellValuePreparators\General;

class CellValuePreparatorFactory
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private Metadata $metadata
    ) {}

    public function create(string $format, string $fieldType): CellValuePreparator
    {
        
        $className = $this->metadata
            ->get(['app', 'export', 'formatDefs', $format, 'cellValuePreparatorClassNameMap', $fieldType]) ??
            General::class;

        return $this->injectableFactory->create($className);
    }
}
