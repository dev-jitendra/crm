<?php


namespace Espo\Core\Record\Select;

use Espo\Core\Select\Applier\AdditionalApplier;
use Espo\Core\Utils\Metadata;

class ApplierClassNameListProvider
{
    public function __construct(private Metadata $metadata)
    {}

    
    public function get(string $entityType): array
    {
        return $this->metadata->get(['recordDefs', $entityType, 'selectApplierClassNameList']) ?? [];
    }
}
