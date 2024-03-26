<?php

namespace Laminas\Ldap;

use ArrayAccess;
use ReturnTypeWillChange;

use function array_change_key_case;
use function array_keys;
use function array_merge;
use function array_pop;
use function array_slice;
use function array_splice;
use function array_unshift;
use function count;
use function implode;
use function in_array;
use function is_array;
use function is_int;
use function is_string;
use function ksort;
use function preg_match;
use function str_replace;
use function strlen;
use function strtolower;
use function strtoupper;
use function substr;
use function trim;

use const CASE_LOWER;
use const CASE_UPPER;
use const SORT_STRING;


class Dn implements ArrayAccess
{
    public const ATTR_CASEFOLD_NONE  = 'none';
    public const ATTR_CASEFOLD_UPPER = 'upper';
    public const ATTR_CASEFOLD_LOWER = 'lower';

    
    protected static $defaultCaseFold = self::ATTR_CASEFOLD_NONE;

    
    protected $caseFold;

    
    protected $dn;

    
    public static function factory($dn, $caseFold = null)
    {
        if (is_array($dn)) {
            return static::fromArray($dn, $caseFold);
        } elseif (is_string($dn)) {
            return static::fromString($dn, $caseFold);
        }
        throw new Exception\LdapException(null, 'Invalid argument type for $dn');
    }

    
    public static function fromString($dn, $caseFold = null)
    {
        $dn = trim($dn);
        if (empty($dn)) {
            $dnArray = [];
        } else {
            $dnArray = static::explodeDn($dn);
        }
        return new static($dnArray, $caseFold);
    }

    
    public static function fromArray(array $dn, $caseFold = null)
    {
        return new static($dn, $caseFold);
    }

    
    protected function __construct(array $dn, $caseFold)
    {
        $this->dn = $dn;
        $this->setCaseFold($caseFold);
    }

    
    public function getRdn($caseFold = null)
    {
        $caseFold = static::sanitizeCaseFold($caseFold, $this->caseFold);
        return static::caseFoldRdn($this->get(0, 1, $caseFold), null);
    }

    
    public function getRdnString($caseFold = null)
    {
        $caseFold = static::sanitizeCaseFold($caseFold, $this->caseFold);
        return static::implodeRdn($this->getRdn(), $caseFold);
    }

    
    public function getParentDn($levelUp = 1)
    {
        $levelUp = (int) $levelUp;
        if ($levelUp < 1 || $levelUp >= count($this->dn)) {
            throw new Exception\LdapException(null, 'Cannot retrieve parent DN with given $levelUp');
        }
        $newDn = array_slice($this->dn, $levelUp);
        return new static($newDn, $this->caseFold);
    }

    
    public function get($index, $length = 1, $caseFold = null)
    {
        $caseFold = static::sanitizeCaseFold($caseFold, $this->caseFold);
        $this->assertIndex($index);
        $length = (int) $length;
        if ($length <= 0) {
            $length = 1;
        }
        if ($length === 1) {
            return static::caseFoldRdn($this->dn[$index], $caseFold);
        }
        return static::caseFoldDn(array_slice($this->dn, $index, $length, false), $caseFold);
    }

    
    public function set($index, array $value)
    {
        $this->assertIndex($index);
        static::assertRdn($value);
        $this->dn[$index] = $value;
        return $this;
    }

    
    public function remove($index, $length = 1)
    {
        $this->assertIndex($index);
        $length = (int) $length;
        if ($length <= 0) {
            $length = 1;
        }
        array_splice($this->dn, $index, $length, null);
        return $this;
    }

    
    public function append(array $value)
    {
        static::assertRdn($value);
        $this->dn[] = $value;
        return $this;
    }

    
    public function prepend(array $value)
    {
        static::assertRdn($value);
        array_unshift($this->dn, $value);
        return $this;
    }

    
    public function insert($index, array $value)
    {
        $this->assertIndex($index);
        static::assertRdn($value);
        $first    = array_slice($this->dn, 0, $index + 1);
        $second   = array_slice($this->dn, $index + 1);
        $this->dn = array_merge($first, [$value], $second);
        return $this;
    }

    
    protected function assertIndex($index)
    {
        if (! is_int($index)) {
            throw new Exception\LdapException(null, 'Parameter $index must be an integer');
        }
        if ($index < 0 || $index >= count($this->dn)) {
            throw new Exception\LdapException(null, 'Parameter $index out of bounds');
        }
        return true;
    }

    
    protected static function assertRdn(array $value)
    {
        if (count($value) < 1) {
            throw new Exception\LdapException(null, 'RDN Array is malformed: it must have at least one item');
        }

        foreach (array_keys($value) as $key) {
            if (! is_string($key)) {
                throw new Exception\LdapException(null, 'RDN Array is malformed: it must use string keys');
            }
        }
    }

    
    public function setCaseFold($caseFold)
    {
        $this->caseFold = static::sanitizeCaseFold($caseFold, static::$defaultCaseFold);
    }

    
    public function toString($caseFold = null)
    {
        $caseFold = static::sanitizeCaseFold($caseFold, $this->caseFold);
        return static::implodeDn($this->dn, $caseFold);
    }

    
    public function toArray($caseFold = null)
    {
        $caseFold = static::sanitizeCaseFold($caseFold, $this->caseFold);

        if ($caseFold === self::ATTR_CASEFOLD_NONE) {
            return $this->dn;
        }
        return static::caseFoldDn($this->dn, $caseFold);
    }

    
    protected static function caseFoldRdn(array $part, $caseFold)
    {
        switch ($caseFold) {
            case self::ATTR_CASEFOLD_UPPER:
                return array_change_key_case($part, CASE_UPPER);
            case self::ATTR_CASEFOLD_LOWER:
                return array_change_key_case($part, CASE_LOWER);
            case self::ATTR_CASEFOLD_NONE:
            default:
                return $part;
        }
    }

    
    protected static function caseFoldDn(array $dn, $caseFold)
    {
        $return = [];
        foreach ($dn as $part) {
            $return[] = static::caseFoldRdn($part, $caseFold);
        }
        return $return;
    }

    
    public function __toString()
    {
        return $this->toString();
    }

    
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        $offset = (int) $offset;
        if ($offset < 0 || $offset >= count($this->dn)) {
            return false;
        }
        return true;
    }

    
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset, 1, null);
    }

    
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->remove($offset, 1);
    }

    
    public static function setDefaultCaseFold($caseFold)
    {
        static::$defaultCaseFold = static::sanitizeCaseFold($caseFold, self::ATTR_CASEFOLD_NONE);
    }

    
    protected static function sanitizeCaseFold($caseFold, $default)
    {
        switch ($caseFold) {
            case self::ATTR_CASEFOLD_NONE:
            case self::ATTR_CASEFOLD_UPPER:
            case self::ATTR_CASEFOLD_LOWER:
                return $caseFold;
            default:
                return $default;
        }
    }

    
    public static function escapeValue($values = [])
    {
        if (! is_array($values)) {
            $values = [$values];
        }
        
        foreach ($values as $key => $val) {
            
            $val = str_replace(
                ['\\', ',', '+', '"', '<', '>', ';', '#', '='],
                ['\\\\', '\,', '\+', '\"', '\<', '\>', '\;', '\#', '\='],
                $val
            );
            $val = Converter\Converter::ascToHex32($val);

            
            if (preg_match('/^(\s*)(.+?)(\s*)$/', $val, $matches)) {
                $val = $matches[2];
                for ($i = 0, $len = strlen($matches[1]); $i < $len; $i++) {
                    $val = '\20' . $val;
                }
                for ($i = 0, $len = strlen($matches[3]); $i < $len; $i++) {
                    $val .= '\20';
                }
            }
            if (null === $val) {
                $val = '\0'; 
            }
            $values[$key] = $val;
        }
        return count($values) === 1 ? $values[0] : $values;
    }

    
    public static function unescapeValue($values = [])
    {
        if (! is_array($values)) {
            $values = [$values];
        }
        
        foreach ($values as $key => $val) {
            
            $val          = str_replace(
                ['\\\\', '\,', '\+', '\"', '\<', '\>', '\;', '\#', '\='],
                ['\\', ',', '+', '"', '<', '>', ';', '#', '='],
                $val
            );
            $values[$key] = Converter\Converter::hex32ToAsc($val);
        }
        return count($values) === 1 ? $values[0] : $values;
    }

    
    public static function explodeDn(
        $dn,
        ?array &$keys = null,
        ?array &$vals = null,
        $caseFold = self::ATTR_CASEFOLD_NONE
    ) {
        $k = [];
        $v = [];
        if (! self::checkDn($dn, $k, $v, $caseFold)) {
            throw new Exception\LdapException(null, 'DN is malformed');
        }
        $ret = [];
        for ($i = 0, $count = count($k); $i < $count; $i++) {
            if (is_array($k[$i]) && is_array($v[$i]) && (($keyCount = count($k[$i])) === count($v[$i]))) {
                $multi = [];
                for ($j = 0; $j < $keyCount; $j++) {
                    $key         = $k[$i][$j];
                    $val         = $v[$i][$j];
                    $multi[$key] = $val;
                }
                $ret[] = $multi;
            } elseif (is_string($k[$i]) && is_string($v[$i])) {
                $ret[] = [$k[$i] => $v[$i]];
            }
        }
        if ($keys !== null) {
            $keys = $k;
        }
        if ($vals !== null) {
            $vals = $v;
        }
        return $ret;
    }

    
    public static function checkDn(
        $dn,
        ?array &$keys = null,
        ?array &$vals = null,
        $caseFold = self::ATTR_CASEFOLD_NONE
    ) {
        
        $slen  = strlen($dn);
        $state = 1;
        $ko    = $vo = 0;
        $multi = false;
        $ka    = [];
        $va    = [];
        for ($di = 0; $di <= $slen; $di++) {
            $ch = $di === $slen ? 0 : $dn[$di];
            switch ($state) {
                case 1: 
                    if ($ch === '=') {
                        $key = trim(substr($dn, $ko, $di - $ko));
                        if ($caseFold === self::ATTR_CASEFOLD_LOWER) {
                            $key = strtolower($key);
                        } elseif ($caseFold === self::ATTR_CASEFOLD_UPPER) {
                            $key = strtoupper($key);
                        }
                        if (is_array($multi)) {
                            $keyId = strtolower($key);
                            if (in_array($keyId, $multi)) {
                                return false;
                            }
                            $ka[count($ka) - 1][] = $key;
                            $multi[]              = $keyId;
                        } else {
                            $ka[] = $key;
                        }
                        $state = 2;
                        $vo    = $di + 1;
                    } elseif ($ch === ',' || $ch === ';' || $ch === '+') {
                        return false;
                    }
                    break;
                case 2: 
                    if ($ch === '\\') {
                        $state = 3;
                    } elseif ($ch === ',' || $ch === ';' || $ch === 0 || $ch === '+') {
                        $value = static::unescapeValue(trim(substr($dn, $vo, $di - $vo)));
                        if (is_array($multi)) {
                            $va[count($va) - 1][] = $value;
                        } else {
                            $va[] = $value;
                        }
                        $state = 1;
                        $ko    = $di + 1;
                        if ($ch === '+' && $multi === false) {
                            $lastKey = array_pop($ka);
                            $lastVal = array_pop($va);
                            $ka[]    = [$lastKey];
                            $va[]    = [$lastVal];
                            $multi   = [strtolower($lastKey)];
                        } elseif ($ch === ',' || $ch === ';' || $ch === 0) {
                            $multi = false;
                        }
                    } elseif ($ch === '=') {
                        return false;
                    }
                    break;
                case 3: 
                    $state = 2;
                    break;
            }
        }

        if ($keys !== null) {
            $keys = $ka;
        }
        if ($vals !== null) {
            $vals = $va;
        }

        return $state === 1 && $ko > 0;
    }

    
    public static function implodeRdn(array $part, $caseFold = null)
    {
        static::assertRdn($part);
        $part     = static::caseFoldRdn($part, $caseFold);
        $rdnParts = [];
        foreach ($part as $key => $value) {
            $value            = static::escapeValue($value);
            $keyId            = strtolower($key);
            $rdnParts[$keyId] = implode('=', [$key, $value]);
        }
        ksort($rdnParts, SORT_STRING);

        return implode('+', $rdnParts);
    }

    
    public static function implodeDn(array $dnArray, $caseFold = null, $separator = ',')
    {
        $parts = [];
        foreach ($dnArray as $p) {
            $parts[] = static::implodeRdn($p, $caseFold);
        }

        return implode($separator, $parts);
    }

    
    public static function isChildOf($childDn, $parentDn)
    {
        try {
            $keys = [];
            $vals = [];
            if ($childDn instanceof Dn) {
                $cdn = $childDn->toArray(self::ATTR_CASEFOLD_LOWER);
            } else {
                $cdn = static::explodeDn($childDn, $keys, $vals, self::ATTR_CASEFOLD_LOWER);
            }
            if ($parentDn instanceof Dn) {
                $pdn = $parentDn->toArray(self::ATTR_CASEFOLD_LOWER);
            } else {
                $pdn = static::explodeDn($parentDn, $keys, $vals, self::ATTR_CASEFOLD_LOWER);
            }
        } catch (Exception\LdapException $e) {
            return false;
        }

        $startIndex = count($cdn) - count($pdn);
        if ($startIndex < 0) {
            return false;
        }
        for ($i = 0, $count = count($pdn); $i < $count; $i++) {
            
            if ($cdn[$i + $startIndex] != $pdn[$i]) { 
                return false;
            }
        }
        return true;
    }
}
