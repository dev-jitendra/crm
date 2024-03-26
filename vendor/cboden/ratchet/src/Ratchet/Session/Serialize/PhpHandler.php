<?php
namespace Ratchet\Session\Serialize;

class PhpHandler implements HandlerInterface {
    
    function serialize(array $data) {
        $preSerialized = array();
        $serialized = '';

        if (count($data)) {
            foreach ($data as $bucket => $bucketData) {
                $preSerialized[] = $bucket . '|' . serialize($bucketData);
            }
            $serialized = implode('', $preSerialized);
        }

        return $serialized;
    }

    
    public function unserialize($raw) {
        $returnData = array();
        $offset     = 0;

        while ($offset < strlen($raw)) {
            if (!strstr(substr($raw, $offset), "|")) {
                throw new \UnexpectedValueException("invalid data, remaining: " . substr($raw, $offset));
            }

            $pos     = strpos($raw, "|", $offset);
            $num     = $pos - $offset;
            $varname = substr($raw, $offset, $num);
            $offset += $num + 1;
            $data    = unserialize(substr($raw, $offset));

            $returnData[$varname] = $data;
            $offset += strlen(serialize($data));
        }

        return $returnData;
    }
}
