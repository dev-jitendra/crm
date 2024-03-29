<?php


class HTMLPurifier_AttrDef_URI_IPv6 extends HTMLPurifier_AttrDef_URI_IPv4
{

    
    public function validate($aIP, $config, $context)
    {
        if (!$this->ip4) {
            $this->_loadRegex();
        }

        $original = $aIP;

        $hex = '[0-9a-fA-F]';
        $blk = '(?:' . $hex . '{1,4})';
        $pre = '(?:/(?:12[0-8]|1[0-1][0-9]|[1-9][0-9]|[0-9]))'; 

        
        if (strpos($aIP, '/') !== false) {
            if (preg_match('#' . $pre . '$#s', $aIP, $find)) {
                $aIP = substr($aIP, 0, 0 - strlen($find[0]));
                unset($find);
            } else {
                return false;
            }
        }

        
        if (preg_match('#(?<=:' . ')' . $this->ip4 . '$#s', $aIP, $find)) {
            $aIP = substr($aIP, 0, 0 - strlen($find[0]));
            $ip = explode('.', $find[0]);
            $ip = array_map('dechex', $ip);
            $aIP .= $ip[0] . $ip[1] . ':' . $ip[2] . $ip[3];
            unset($find, $ip);
        }

        
        $aIP = explode('::', $aIP);
        $c = count($aIP);
        if ($c > 2) {
            return false;
        } elseif ($c == 2) {
            list($first, $second) = $aIP;
            $first = explode(':', $first);
            $second = explode(':', $second);

            if (count($first) + count($second) > 8) {
                return false;
            }

            while (count($first) < 8) {
                array_push($first, '0');
            }

            array_splice($first, 8 - count($second), 8, $second);
            $aIP = $first;
            unset($first, $second);
        } else {
            $aIP = explode(':', $aIP[0]);
        }
        $c = count($aIP);

        if ($c != 8) {
            return false;
        }

        
        foreach ($aIP as $piece) {
            if (!preg_match('#^[0-9a-fA-F]{4}$#s', sprintf('%04s', $piece))) {
                return false;
            }
        }
        return $original;
    }
}


