<?php

namespace Laminas\Ldap\Ldif;

use Laminas\Ldap;

use function array_change_key_case;
use function array_key_exists;
use function array_merge;
use function base64_decode;
use function base64_encode;
use function chunk_split;
use function count;
use function explode;
use function in_array;
use function is_array;
use function is_numeric;
use function is_scalar;
use function ksort;
use function ord;
use function preg_match;
use function rtrim;
use function sprintf;
use function strlen;
use function strtolower;
use function substr;
use function trim;

use const CASE_LOWER;
use const PHP_EOL;
use const SORT_STRING;


class Encoder
{
    
    protected $options = [
        'sort'    => true,
        'version' => 1,
        'wrap'    => 78,
    ];

    
    protected $versionWritten = false;

    
    protected function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    
    public static function decode($string)
    {
        $encoder = new static([]);
        return $encoder->_decode($string);
    }

    
    
    protected function _decode($string)
    {
        
        $items     = [];
        $item      = [];
        $last      = null;
        $inComment = false;
        foreach (explode("\n", $string) as $line) {
            $line    = rtrim($line, "\x09\x0A\x0D\x00\x0B");
            $matches = [];
            if (substr($line, 0, 1) === ' ' && $last !== null && ! $inComment) {
                $last[2] .= substr($line, 1);
            } elseif (substr($line, 0, 1) === '#') {
                $inComment = true;
                continue;
            } elseif (preg_match('/^([a-z0-9;-]+)(:[:<]?\s*)([^<]*)$/i', $line, $matches)) {
                $inComment = false;
                $name      = strtolower($matches[1]);
                $type      = trim($matches[2]);
                $value     = $matches[3];
                if ($last !== null) {
                    $this->pushAttribute($last, $item);
                }
                if ($name === 'version') {
                    continue;
                } elseif (count($item) > 0 && $name === 'dn') {
                    $items[] = $item;
                    $item    = [];
                }
                $last = [$name, $type, $value];
            } elseif (trim($line) === '') {
                continue;
            }
        }
        if ($last !== null) {
            $this->pushAttribute($last, $item);
        }
        $items[] = $item;

        return count($items) > 1 ? $items : $items[0];
    }

    
    protected function pushAttribute(array $attribute, array &$entry)
    {
        $name  = $attribute[0];
        $type  = $attribute[1];
        $value = $attribute[2];
        if ($type === '::') {
            $value = base64_decode($value);
        }
        if ($name === 'dn') {
            $entry[$name] = $value;
        } elseif (isset($entry[$name]) && $value !== '') {
            $entry[$name][] = $value;
        } else {
            $entry[$name] = $value !== '' ? [$value] : [];
        }
    }

    
    public static function encode($value, array $options = [])
    {
        $encoder = new static($options);

        return $encoder->_encode($value);
    }

    
    
    protected function _encode($value)
    {
        
        if (is_scalar($value)) {
            return $this->encodeString($value);
        } elseif (is_array($value)) {
            return $this->encodeAttributes($value);
        } elseif ($value instanceof Ldap\Node) {
            return $value->toLdif($this->options);
        }

        return null;
    }

    
    protected function encodeString($string, &$base64 = null)
    {
        $string = (string) $string;
        if (! is_numeric($string) && empty($string)) {
            return '';
        }

        
        $unsafeInitChar = [0, 10, 13, 32, 58, 60];
        
        $unsafeChar = [0, 10, 13];

        $base64 = false;
        for ($i = 0, $len = strlen($string); $i < $len; $i++) {
            $char = ord(substr($string, $i, 1));
            if ($char >= 127) {
                $base64 = true;
                break;
            } elseif ($i === 0 && in_array($char, $unsafeInitChar)) {
                $base64 = true;
                break;
            } elseif (in_array($char, $unsafeChar)) {
                $base64 = true;
                break;
            }
        }
        
        if (substr($string, -1) === ' ') {
            $base64 = true;
        }

        if ($base64 === true) {
            $string = base64_encode($string);
        }

        return $string;
    }

    
    protected function encodeAttribute($name, $value)
    {
        if (! is_array($value)) {
            $value = [$value];
        }

        $output = '';

        if (count($value) < 1) {
            return $name . ': ';
        }

        foreach ($value as $v) {
            $base64    = null;
            $v         = $this->encodeString($v, $base64);
            $attribute = $name . ':';
            if ($base64 === true) {
                $attribute .= ': ' . $v;
            } else {
                $attribute .= ' ' . $v;
            }
            if (isset($this->options['wrap']) && strlen($attribute) > $this->options['wrap']) {
                $attribute = trim(chunk_split($attribute, $this->options['wrap'], PHP_EOL . ' '));
            }
            $output .= $attribute . PHP_EOL;
        }

        return trim($output, PHP_EOL);
    }

    
    protected function encodeAttributes(array $attributes)
    {
        $string     = '';
        $attributes = array_change_key_case($attributes, CASE_LOWER);
        if (
            ! $this->versionWritten && array_key_exists('dn', $attributes) && isset($this->options['version'])
            && array_key_exists('objectclass', $attributes)
        ) {
            $string              .= sprintf('version: %d', $this->options['version']) . PHP_EOL;
            $this->versionWritten = true;
        }

        if (isset($this->options['sort']) && $this->options['sort'] === true) {
            ksort($attributes, SORT_STRING);
            if (array_key_exists('objectclass', $attributes)) {
                $oc = $attributes['objectclass'];
                unset($attributes['objectclass']);
                $attributes = array_merge(['objectclass' => $oc], $attributes);
            }
            if (array_key_exists('dn', $attributes)) {
                $dn = $attributes['dn'];
                unset($attributes['dn']);
                $attributes = array_merge(['dn' => $dn], $attributes);
            }
        }
        foreach ($attributes as $key => $value) {
            $string .= $this->encodeAttribute($key, $value) . PHP_EOL;
        }

        return trim($string, PHP_EOL);
    }
}
