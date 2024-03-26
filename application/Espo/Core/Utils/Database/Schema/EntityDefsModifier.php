<?php


namespace Espo\Core\Utils\Database\Schema;

use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;
use Espo\ORM\Defs\EntityDefs as OrmEntityDefs;


interface EntityDefsModifier
{
    public function modify(OrmEntityDefs $entityDefs): EntityDefs;
}
