<?php

namespace Laminas\Ldap;

use DateTime;
use Traversable;

use function array_keys;
use function array_merge;
use function array_unique;
use function array_values;
use function base64_encode;
use function count;
use function function_exists;
use function iconv;
use function in_array;
use function is_array;
use function is_int;
use function is_scalar;
use function is_string;
use function mb_convert_encoding;
use function md5;
use function mt_rand;
use function sha1;
use function strlen;
use function strtolower;
use function substr;
use function uniqid;


class Attribute
{
    public const PASSWORD_HASH_MD5   = 'md5';
    public const PASSWORD_HASH_SMD5  = 'smd5';
    public const PASSWORD_HASH_SHA   = 'sha';
    public const PASSWORD_HASH_SSHA  = 'ssha';
    public const PASSWORD_UNICODEPWD = 'unicodePwd';

    
    public static function setAttribute(array &$data, $attribName, $value, $append = false)
    {
        $attribName = strtolower($attribName);
        $valArray   = [];
        if (is_array($value) || $value instanceof Traversable) {
            foreach ($value as $v) {
                $v = self::valueToLdap($v);
                if ($v !== null) {
                    $valArray[] = $v;
                }
            }
        } elseif ($value !== null) {
            $value = self::valueToLdap($value);
            if ($value !== null) {
                $valArray[] = $value;
            }
        }

        if ($append === true && isset($data[$attribName])) {
            if (is_string($data[$attribName])) {
                $data[$attribName] = [$data[$attribName]];
            }
            $data[$attribName] = array_merge($data[$attribName], $valArray);
        } else {
            $data[$attribName] = $valArray;
        }
    }

    
    public static function getAttribute(array $data, $attribName, $index = null)
    {
        $attribName = strtolower($attribName);
        if ($index === null) {
            if (! isset($data[$attribName])) {
                return [];
            }
            $retArray = [];
            foreach ($data[$attribName] as $v) {
                $retArray[] = self::valueFromLdap($v);
            }
            return $retArray;
        } elseif (is_int($index)) {
            if (! isset($data[$attribName])) {
                return;
            } elseif ($index >= 0 && $index < count($data[$attribName])) {
                return self::valueFromLdap($data[$attribName][$index]);
            } else {
                return;
            }
        }

        return null;
    }

    
    public static function attributeHasValue(array &$data, $attribName, $value)
    {
        $attribName = strtolower($attribName);
        if (! isset($data[$attribName])) {
            return false;
        }

        if (is_scalar($value)) {
            $value = [$value];
        }

        foreach ($value as $v) {
            $v = self::valueToLdap($v);
            if (! in_array($v, $data[$attribName], true)) {
                return false;
            }
        }

        return true;
    }

    
    public static function removeDuplicatesFromAttribute(array &$data, $attribName)
    {
        $attribName = strtolower($attribName);
        if (! isset($data[$attribName])) {
            return;
        }
        $data[$attribName] = array_values(array_unique($data[$attribName]));
    }

    
    public static function removeFromAttribute(array &$data, $attribName, $value)
    {
        $attribName = strtolower($attribName);
        if (! isset($data[$attribName])) {
            return;
        }

        if (is_scalar($value)) {
            $value = [$value];
        }

        $valArray = [];
        foreach ($value as $v) {
            $v = self::valueToLdap($v);
            if ($v !== null) {
                $valArray[] = $v;
            }
        }

        $resultArray = $data[$attribName];
        foreach ($valArray as $rv) {
            $keys = array_keys($resultArray, $rv);
            foreach ($keys as $k) {
                unset($resultArray[$k]);
            }
        }
        $resultArray       = array_values($resultArray);
        $data[$attribName] = $resultArray;
    }

    
    private static function valueToLdap($value)
    {
        return Converter\Converter::toLdap($value);
    }

    
    private static function valueFromLdap($value)
    {
        try {
            $return = Converter\Converter::fromLdap($value, Converter\Converter::STANDARD, false);
            if ($return instanceof DateTime) {
                return Converter\Converter::toLdapDateTime($return, false);
            } else {
                return $return;
            }
        } catch (Converter\Exception\InvalidArgumentException $e) {
            return $value;
        }
    }

    
    public static function setPassword(
        array &$data,
        $password,
        $hashType = self::PASSWORD_HASH_MD5,
        $attribName = null
    ) {
        if ($attribName === null) {
            if ($hashType === self::PASSWORD_UNICODEPWD) {
                $attribName = 'unicodePwd';
            } else {
                $attribName = 'userPassword';
            }
        }

        $hash = static::createPassword($password, $hashType);
        static::setAttribute($data, $attribName, $hash, false);
    }

    
    public static function createPassword($password, $hashType = self::PASSWORD_HASH_MD5)
    {
        switch ($hashType) {
            case self::PASSWORD_UNICODEPWD:
                
                $password = '"' . $password . '"';
                if (function_exists('mb_convert_encoding')) {
                    $password = mb_convert_encoding($password, 'UTF-16LE', 'UTF-8');
                } elseif (function_exists('iconv')) {
                    $password = iconv('UTF-8', 'UTF-16LE', $password);
                } else {
                    $len = strlen($password);
                    $new = '';
                    for ($i = 0; $i < $len; $i++) {
                        $new .= $password[$i] . "\x00";
                    }
                    $password = $new;
                }
                return $password;
            case self::PASSWORD_HASH_SSHA:
                $salt    = substr(sha1(uniqid(mt_rand(), true), true), 0, 4);
                $rawHash = sha1($password . $salt, true) . $salt;
                $method  = '{SSHA}';
                break;
            case self::PASSWORD_HASH_SHA:
                $rawHash = sha1($password, true);
                $method  = '{SHA}';
                break;
            case self::PASSWORD_HASH_SMD5:
                $salt    = substr(sha1(uniqid(mt_rand(), true), true), 0, 4);
                $rawHash = md5($password . $salt, true) . $salt;
                $method  = '{SMD5}';
                break;
            case self::PASSWORD_HASH_MD5:
            default:
                $rawHash = md5($password, true);
                $method  = '{MD5}';
                break;
        }
        return $method . base64_encode($rawHash);
    }

    
    public static function setDateTimeAttribute(
        array &$data,
        $attribName,
        $value,
        $utc = false,
        $append = false
    ) {
        $convertedValues = [];
        if (is_array($value) || $value instanceof Traversable) {
            foreach ($value as $v) {
                $v = self::valueToLdapDateTime($v, $utc);
                if ($v !== null) {
                    $convertedValues[] = $v;
                }
            }
        } elseif ($value !== null) {
            $value = self::valueToLdapDateTime($value, $utc);
            if ($value !== null) {
                $convertedValues[] = $value;
            }
        }
        static::setAttribute($data, $attribName, $convertedValues, $append);
    }

    
    private static function valueToLdapDateTime($value, $utc)
    {
        if (! is_int($value)) {
            return null;
        }

        return Converter\Converter::toLdapDateTime($value, $utc);
    }

    
    public static function getDateTimeAttribute(array $data, $attribName, $index = null)
    {
        $values = static::getAttribute($data, $attribName, $index);
        if (is_array($values)) {
            for ($i = 0, $count = count($values); $i < $count; $i++) {
                $newVal = self::valueFromLdapDateTime($values[$i]);
                if ($newVal !== null) {
                    $values[$i] = $newVal;
                }
            }
        } else {
            $newVal = self::valueFromLdapDateTime($values);
            if ($newVal !== null) {
                $values = $newVal;
            }
        }

        return $values;
    }

    
    private static function valueFromLdapDateTime($value)
    {
        if ($value instanceof DateTime) {
            return $value->format('U');
        }

        if (! is_string($value)) {
            return null;
        }

        try {
            return Converter\Converter::fromLdapDateTime($value, false)->format('U');
        } catch (Converter\Exception\InvalidArgumentException $e) {
            return null;
        }
    }
}
