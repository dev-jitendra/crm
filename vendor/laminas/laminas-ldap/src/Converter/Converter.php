<?php

namespace Laminas\Ldap\Converter;

use DateTime;
use DateTimeZone;
use Exception as PHPException;
use Laminas\Ldap\ErrorHandler;

use function chr;
use function date_default_timezone_get;
use function dechex;
use function get_resource_type;
use function hexdec;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_numeric;
use function is_object;
use function is_resource;
use function is_scalar;
use function is_string;
use function ord;
use function preg_match;
use function preg_replace_callback;
use function serialize;
use function str_pad;
use function str_replace;
use function stream_get_contents;
use function strlen;
use function strtolower;
use function substr;
use function unserialize;

use const E_NOTICE;
use const STR_PAD_LEFT;


class Converter
{
    public const STANDARD         = 0;
    public const BOOLEAN          = 1;
    public const GENERALIZED_TIME = 2;

    
    public static function ascToHex32($string)
    {
        for ($i = 0, $len = strlen($string); $i < $len; $i++) {
            $char = substr($string, $i, 1);
            if (ord($char) < 32) {
                $hex = dechex(ord($char));
                if (strlen($hex) === 1) {
                    $hex = '0' . $hex;
                }
                $string = str_replace($char, '\\' . $hex, $string);
            }
        }
        return $string;
    }

    
    public static function hex32ToAsc($string)
    {
        $string = preg_replace_callback(
            '/\\\([0-9A-Fa-f]{2})/',
            static fn($matches): string => chr(hexdec($matches[1])),
            $string
        );
        return $string;
    }

    
    public static function toLdap($value, $type = self::STANDARD)
    {
        try {
            switch ($type) {
                case self::BOOLEAN:
                    return static::toLdapBoolean($value);
                case self::GENERALIZED_TIME:
                    return static::toLdapDatetime($value);
                default:
                    if (is_string($value)) {
                        return $value;
                    } elseif (is_int($value) || is_float($value)) {
                        return (string) $value;
                    } elseif (is_bool($value)) {
                        return static::toLdapBoolean($value);
                    } elseif (is_object($value)) {
                        if ($value instanceof DateTime) {
                            return static::toLdapDatetime($value);
                        } else {
                            return static::toLdapSerialize($value);
                        }
                    } elseif (is_array($value)) {
                        return static::toLdapSerialize($value);
                    } elseif (is_resource($value) && get_resource_type($value) === 'stream') {
                        return stream_get_contents($value);
                    }

                    return null;
            }
        } catch (PHPException $e) {
            throw new Exception\ConverterException($e->getMessage(), $e->getCode(), $e);
        }
    }

    
    public static function toLdapDateTime($date, $asUtc = true)
    {
        if (! $date instanceof DateTime) {
            if (is_int($date)) {
                $date = new DateTime('@' . $date);
                $date->setTimezone(new DateTimeZone(date_default_timezone_get()));
            } elseif (is_string($date)) {
                $date = new DateTime($date);
            } else {
                throw new Exception\InvalidArgumentException('Parameter $date is not of the expected type');
            }
        }
        $timezone = $date->format('O');
        if (true === $asUtc) {
            $date->setTimezone(new DateTimeZone('UTC'));
            $timezone = 'Z';
        }
        if ('+0000' === $timezone) {
            $timezone = 'Z';
        }
        return $date->format('YmdHis') . $timezone;
    }

    
    public static function toLdapBoolean($value)
    {
        $return = 'FALSE';
        if (! is_scalar($value)) {
            return $return;
        }
        if (true === $value || (is_string($value) && 'true' === strtolower($value)) || 1 === $value) {
            $return = 'TRUE';
        }
        return $return;
    }

    
    public static function toLdapSerialize($value)
    {
        return serialize($value);
    }

    
    public static function fromLdap($value, $type = self::STANDARD, $dateTimeAsUtc = true)
    {
        switch ($type) {
            case self::BOOLEAN:
                return static::fromldapBoolean($value);
            case self::GENERALIZED_TIME:
                return static::fromLdapDateTime($value);
            default:
                if (is_numeric($value)) {
                    
                    return $value;
                } elseif ('TRUE' === $value || 'FALSE' === $value) {
                    return static::fromLdapBoolean($value);
                }
                if (preg_match('/^\d{4}[\d\+\-Z\.]*$/', $value)) {
                    return static::fromLdapDateTime($value, $dateTimeAsUtc);
                }
                try {
                    return static::fromLdapUnserialize($value);
                } catch (Exception\UnexpectedValueException $e) {
                    
                }
                break;
        }

        return $value;
    }

    
    public static function fromLdapDateTime($date, $asUtc = true)
    {
        $datepart = [];
        if (! preg_match('/^(\d{4})/', $date, $datepart)) {
            throw new Exception\InvalidArgumentException('Invalid date format found');
        }

        if ($datepart[1] < 4) {
            throw new Exception\InvalidArgumentException('Invalid date format found (too short)');
        }

        $time = [
            
            'year'          => $datepart[1],
            'month'         => 1,
            'day'           => 1,
            'hour'          => 0,
            'minute'        => 0,
            'second'        => 0,
            'offdir'        => '+',
            'offsethours'   => 0,
            'offsetminutes' => 0,
        ];

        $length = strlen($date);

        
        if ($length >= 6) {
            $month = substr($date, 4, 2);
            if ($month < 1 || $month > 12) {
                throw new Exception\InvalidArgumentException('Invalid date format found (invalid month)');
            }
            $time['month'] = $month;
        }

        
        if ($length >= 8) {
            $day = substr($date, 6, 2);
            if ($day < 1 || $day > 31) {
                throw new Exception\InvalidArgumentException('Invalid date format found (invalid day)');
            }
            $time['day'] = $day;
        }

        
        if ($length >= 10) {
            $hour = substr($date, 8, 2);
            if ($hour < 0 || $hour > 23) {
                throw new Exception\InvalidArgumentException('Invalid date format found (invalid hour)');
            }
            $time['hour'] = $hour;
        }

        
        if ($length >= 12) {
            $minute = substr($date, 10, 2);
            if ($minute < 0 || $minute > 59) {
                throw new Exception\InvalidArgumentException('Invalid date format found (invalid minute)');
            }
            $time['minute'] = $minute;
        }

        
        if ($length >= 14) {
            $second = substr($date, 12, 2);
            if ($second < 0 || $second > 59) {
                throw new Exception\InvalidArgumentException('Invalid date format found (invalid second)');
            }
            $time['second'] = $second;
        }

        
        $offsetRegEx = '/([Z\-\+])(\d{2}\'?){0,1}(\d{2}\'?){0,1}$/';
        $off         = [];
        if (preg_match($offsetRegEx, $date, $off)) {
            $offset = $off[1];
            if ($offset === '+' || $offset === '-') {
                $time['offdir'] = $offset;
                
                if (isset($off[2])) {
                    $offsetHours = substr($off[2], 0, 2);
                    if ($offsetHours < 0 || $offsetHours > 12) {
                        throw new Exception\InvalidArgumentException('Invalid date format found (invalid offset hour)');
                    }
                    $time['offsethours'] = $offsetHours;
                }
                if (isset($off[3])) {
                    $offsetMinutes = substr($off[3], 0, 2);
                    if ($offsetMinutes < 0 || $offsetMinutes > 59) {
                        throw new Exception\InvalidArgumentException(
                            'Invalid date format found (invalid offset minute)'
                        );
                    }
                    $time['offsetminutes'] = $offsetMinutes;
                }
            }
        }

        
        $timestring = $time['year'] . '-'
                      . str_pad($time['month'], 2, '0', STR_PAD_LEFT) . '-'
                      . str_pad($time['day'], 2, '0', STR_PAD_LEFT) . ' '
                      . str_pad($time['hour'], 2, '0', STR_PAD_LEFT) . ':'
                      . str_pad($time['minute'], 2, '0', STR_PAD_LEFT) . ':'
                      . str_pad($time['second'], 2, '0', STR_PAD_LEFT)
                      . $time['offdir']
                      . str_pad($time['offsethours'], 2, '0', STR_PAD_LEFT)
                      . str_pad($time['offsetminutes'], 2, '0', STR_PAD_LEFT);
        try {
            $date = new DateTime($timestring);
        } catch (PHPException $e) {
            throw new Exception\InvalidArgumentException(
                'Invalid date format found',
                0,
                $e
            );
        }
        if ($asUtc) {
            $date->setTimezone(new DateTimeZone('UTC'));
        }
        return $date;
    }

    
    public static function fromLdapBoolean($value)
    {
        if ('TRUE' === $value) {
            return true;
        } elseif ('FALSE' === $value) {
            return false;
        } else {
            throw new Exception\InvalidArgumentException('The given value is not a boolean value');
        }
    }

    
    public static function fromLdapUnserialize($value)
    {
        ErrorHandler::start(E_NOTICE);
        $v = unserialize($value);
        ErrorHandler::stop();

        if (false === $v && $value !== 'b:0;') {
            throw new Exception\UnexpectedValueException('The given value could not be unserialized');
        }
        return $v;
    }
}
