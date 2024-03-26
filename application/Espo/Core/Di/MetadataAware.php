<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\Metadata;

interface MetadataAware
{
    public function setMetadata(Metadata $metadata): void;
}
