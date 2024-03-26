<?php

namespace Doctrine\Common\Cache;


interface MultiGetCache
{
    
    public function fetchMultiple(array $keys);
}
