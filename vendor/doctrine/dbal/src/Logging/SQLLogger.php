<?php

namespace Doctrine\DBAL\Logging;

use Doctrine\DBAL\Types\Type;


interface SQLLogger
{
    
    public function startQuery($sql, ?array $params = null, ?array $types = null);

    
    public function stopQuery();
}
