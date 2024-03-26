<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Driver;

use Throwable;


interface Exception extends Throwable
{
    
    public function getSQLState();
}
