<?php

namespace Doctrine\DBAL\Schema;

use Doctrine\DBAL\Platforms\AbstractPlatform;


interface Constraint
{
    
    public function getName();

    
    public function getQuotedName(AbstractPlatform $platform);

    
    public function getColumns();

    
    public function getQuotedColumns(AbstractPlatform $platform);
}
