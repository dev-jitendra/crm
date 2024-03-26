<?php


namespace Espo\Core\Utils\Database\Orm;

use Espo\ORM\Defs\IndexDefs;

interface IndexHelper
{
    
    public function composeKey(IndexDefs $defs, string $entityType): string;
}
