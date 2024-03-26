<?php

namespace Doctrine\Common\Cache;


interface MultiPutCache
{
    
    public function saveMultiple(array $keysAndValues, $lifetime = 0);
}
