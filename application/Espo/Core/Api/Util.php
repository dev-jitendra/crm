<?php


namespace Espo\Core\Api;

use Espo\Core\Utils\Config;
use stdClass;

class Util
{
    public function __construct(private Config $config) {}

    public static function cloneObject(stdClass $source): stdClass
    {
        $cloned = (object) [];

        foreach (get_object_vars($source) as $k => $v) {
            $cloned->$k = self::cloneObjectItem($v);
        }

        return $cloned;
    }

    
    private static function cloneObjectItem($item)
    {
        if (is_array($item)) {
            $cloned = [];

            foreach ($item as $v) {
                $cloned[] = self::cloneObjectItem($v);
            }

            return $cloned;
        }

        if ($item instanceof stdClass) {
            return self::cloneObject($item);
        }

        if (is_object($item)) {
            return clone $item;
        }

        return $item;
    }

    public function obtainIpFromRequest(Request $request): ?string
    {
        $param = $this->config->get('ipAddressServerParam') ?? 'REMOTE_ADDR';

        return $request->getServerParam($param);
    }
}
