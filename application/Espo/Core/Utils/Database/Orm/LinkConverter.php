<?php


namespace Espo\Core\Utils\Database\Orm;

use Espo\ORM\Defs\RelationDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;


interface LinkConverter
{
    public function convert(RelationDefs $linkDefs, string $entityType): EntityDefs;
}
