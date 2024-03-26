<?php


namespace Espo\Core\Utils;

use stdClass;

class ObjectUtil
{
    
    public static function clone(stdClass $source): stdClass
    {
        $cloned = (object) [];

        foreach (get_object_vars($source) as $k => $v) {
            $cloned->$k = self::cloneItem($v);
        }

        return $cloned;
    }

    
    private static function cloneItem($item)
    {
        if (is_array($item)) {
            $cloned = [];

            foreach ($item as $i => $v) {
                $cloned[$i] = self::cloneItem($v);
            }

            return $cloned;
        }

        if ($item instanceof stdClass) {
            return self::clone($item);
        }

        if (is_object($item)) {
            return clone $item;
        }

        return $item;
    }
}
