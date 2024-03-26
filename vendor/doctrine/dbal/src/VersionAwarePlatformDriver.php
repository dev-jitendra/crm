<?php

namespace Doctrine\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;


interface VersionAwarePlatformDriver extends Driver
{
    
    public function createDatabasePlatformForVersion($version);
}
