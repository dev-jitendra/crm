<?php


namespace Espo\Core\Utils\Database\Orm\IndexHelpers;

use Espo\Core\Utils\Database\Orm\IndexHelper;
use Espo\Core\Utils\Util;
use Espo\ORM\Defs\IndexDefs;

class MysqlIndexHelper implements IndexHelper
{
    private const MAX_LENGTH = 60;

    public function composeKey(IndexDefs $defs, string $entityType): string
    {
        $name = $defs->getName();
        $prefix = $defs->isUnique() ? 'UNIQ' : 'IDX';

        $parts = [$prefix, strtoupper(Util::toUnderScore($name))];

        $key = implode('_', $parts);

        return substr($key, 0, self::MAX_LENGTH);
    }
}
