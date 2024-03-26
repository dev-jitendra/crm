<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\DataCache;

interface DataCacheAware
{
    public function setDataCache(DataCache $dataCache): void;
}
