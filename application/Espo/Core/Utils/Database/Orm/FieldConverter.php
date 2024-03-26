<?php


namespace Espo\Core\Utils\Database\Orm;

use Espo\ORM\Defs\FieldDefs;
use Espo\Core\Utils\Database\Orm\Defs\EntityDefs;


interface FieldConverter
{
    public function convert(FieldDefs $fieldDefs, string $entityType): EntityDefs;
}
