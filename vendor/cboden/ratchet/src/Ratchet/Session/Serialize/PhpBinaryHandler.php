<?php
namespace Ratchet\Session\Serialize;

class PhpBinaryHandler implements HandlerInterface {
    
    function serialize(array $data) {
        throw new \RuntimeException("Serialize PhpHandler:serialize code not written yet, write me!");
    }

    
    public function unserialize($raw) {
        $returnData = array();
        $offset     = 0;

        while ($offset < strlen($raw)) {
            $num     = ord($raw[$offset]);
            $offset += 1;
            $varname = substr($raw, $offset, $num);
            $offset += $num;
            $data    = unserialize(substr($raw, $offset));

            $returnData[$varname] = $data;
            $offset += strlen(serialize($data));
        }

        return $returnData;
    }
}
