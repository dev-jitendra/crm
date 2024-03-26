<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\Metadata;

trait MetadataSetter
{
    
    protected $metadata;

    public function setMetadata(Metadata $metadata): void
    {
        $this->metadata = $metadata;
    }
}
