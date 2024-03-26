<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\DataCache;

trait DataCacheSetter
{
    
    protected $dataCache;

    public function setDataCache(DataCache $dataCache): void
    {
        $this->dataCache = $dataCache;
    }
}
