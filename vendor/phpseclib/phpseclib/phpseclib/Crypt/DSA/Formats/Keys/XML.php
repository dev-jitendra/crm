<?php



namespace phpseclib3\Crypt\DSA\Formats\Keys;

use phpseclib3\Common\Functions\Strings;
use phpseclib3\Exception\BadConfigurationException;
use phpseclib3\Math\BigInteger;


abstract class XML
{
    
    public static function load($key, $password = '')
    {
        if (!Strings::is_stringable($key)) {
            throw new \UnexpectedValueException('Key should be a string - not a ' . gettype($key));
        }

        if (!class_exists('DOMDocument')) {
            throw new BadConfigurationException('The dom extension is not setup correctly on this system');
        }

        $use_errors = libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        if (substr($key, 0, 5) != '<?xml') {
            $key = '<xml>' . $key . '</xml>';
        }
        if (!$dom->loadXML($key)) {
            libxml_use_internal_errors($use_errors);
            throw new \UnexpectedValueException('Key does not appear to contain XML');
        }
        $xpath = new \DOMXPath($dom);
        $keys = ['p', 'q', 'g', 'y', 'j', 'seed', 'pgencounter'];
        foreach ($keys as $key) {
            
            $temp = $xpath->query("
            if (!$temp->length) {
                continue;
            }
            $value = new BigInteger(Strings::base64_decode($temp->item(0)->nodeValue), 256);
            switch ($key) {
                case 'p': 
                    
                    
                    
                    $components['p'] = $value;
                    break;
                case 'q': 
                    $components['q'] = $value;
                    break;
                case 'g': 
                    $components['g'] = $value;
                    break;
                case 'y': 
                    $components['y'] = $value;
                    
                case 'j': 
                    
                    
                case 'seed': 
                    
                    
                    
                case 'pgencounter': 
            }
        }

        libxml_use_internal_errors($use_errors);

        if (!isset($components['y'])) {
            throw new \UnexpectedValueException('Key is missing y component');
        }

        switch (true) {
            case !isset($components['p']):
            case !isset($components['q']):
            case !isset($components['g']):
                return ['y' => $components['y']];
        }

        return $components;
    }

    
    public static function savePublicKey(BigInteger $p, BigInteger $q, BigInteger $g, BigInteger $y)
    {
        return "<DSAKeyValue>\r\n" .
               '  <P>' . Strings::base64_encode($p->toBytes()) . "</P>\r\n" .
               '  <Q>' . Strings::base64_encode($q->toBytes()) . "</Q>\r\n" .
               '  <G>' . Strings::base64_encode($g->toBytes()) . "</G>\r\n" .
               '  <Y>' . Strings::base64_encode($y->toBytes()) . "</Y>\r\n" .
               '</DSAKeyValue>';
    }
}
