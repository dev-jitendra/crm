<?php


namespace Espo\Core\Utils\Database\Schema;

use Espo\ORM\Defs\AttributeDefs;

interface ColumnPreparator
{
    public function prepare(AttributeDefs $defs): Column;
}
