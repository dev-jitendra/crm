<?php


namespace Espo\Core\Utils\Database\Orm\IndexHelpers;

use Espo\Core\Utils\Database\Orm\IndexHelper;
use Espo\Core\Utils\Util;
use Espo\ORM\Defs\IndexDefs;

class PostgresqlIndexHelper implements IndexHelper
{
    private const MAX_LENGTH = 59;

    public function composeKey(IndexDefs $defs, string $entityType): string
    {
        $name = $defs->getName();
        $prefix = $defs->isUnique() ? 'UNIQ' : 'IDX';

        $parts = [
            $prefix,
            strtoupper(Util::toUnderScore($entityType)),
            strtoupper(Util::toUnderScore($name)),
        ];

        $key = implode('_', $parts);

        return self::decreaseLength($key);
    }

    private static function decreaseLength(string $key): string
    {
        if (strlen($key) <= self::MAX_LENGTH) {
            return $key;
        }

        $list = explode('_', $key);

        $maxItemLength = 0;
        foreach ($list as $item) {
            if (strlen($item) > $maxItemLength) {
                $maxItemLength = strlen($item);
            }
        }
        $maxItemLength--;

        $list = array_map(
            fn ($item) => substr($item, 0, min($maxItemLength, strlen($item))),
            $list
        );

        $key = implode('_', $list);

        return self::decreaseLength($key);
    }
}
