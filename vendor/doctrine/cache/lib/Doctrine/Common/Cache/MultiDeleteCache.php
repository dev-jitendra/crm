<?php

namespace Doctrine\Common\Cache;


interface MultiDeleteCache
{
    
    public function deleteMultiple(array $keys);
}
