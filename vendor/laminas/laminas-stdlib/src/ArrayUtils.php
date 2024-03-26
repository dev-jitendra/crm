<?php 


declare(strict_types=1);

namespace Laminas\Stdlib;

use Iterator;
use Laminas\Stdlib\ArrayUtils\MergeRemoveKey;
use Laminas\Stdlib\ArrayUtils\MergeReplaceKeyInterface;
use Traversable;

use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_values;
use function in_array;
use function is_array;
use function is_callable;
use function is_float;
use function is_int;
use function is_object;
use function is_scalar;
use function is_string;
use function iterator_to_array;
use function method_exists;
use function sprintf;


abstract class ArrayUtils
{
    
    public const ARRAY_FILTER_USE_BOTH = 1;

    
    public const ARRAY_FILTER_USE_KEY = 2;

    
    public static function hasStringKeys(mixed $value, $allowEmpty = false)
    {
        if (! is_array($value)) {
            return false;
        }

        if (! $value) {
            return $allowEmpty;
        }

        return [] !== array_filter(array_keys($value), 'is_string');
    }

    
    public static function hasIntegerKeys(mixed $value, $allowEmpty = false)
    {
        if (! is_array($value)) {
            return false;
        }

        if (! $value) {
            return $allowEmpty;
        }

        return [] !== array_filter(array_keys($value), 'is_int');
    }

    
    public static function hasNumericKeys(mixed $value, $allowEmpty = false)
    {
        if (! is_array($value)) {
            return false;
        }

        if (! $value) {
            return $allowEmpty;
        }

        return [] !== array_filter(array_keys($value), 'is_numeric');
    }

    
    public static function isList(mixed $value, $allowEmpty = false)
    {
        if (! is_array($value)) {
            return false;
        }

        if (! $value) {
            return $allowEmpty;
        }

        return array_values($value) === $value;
    }

    
    public static function isHashTable(mixed $value, $allowEmpty = false)
    {
        if (! is_array($value)) {
            return false;
        }

        if (! $value) {
            return $allowEmpty;
        }

        return array_values($value) !== $value;
    }

    
    public static function inArray(mixed $needle, array $haystack, $strict = false)
    {
        if (! $strict) {
            if (is_int($needle) || is_float($needle)) {
                $needle = (string) $needle;
            }
            if (is_string($needle)) {
                foreach ($haystack as &$h) {
                    if (is_int($h) || is_float($h)) {
                        $h = (string) $h;
                    }
                }
            }
        }

        return in_array($needle, $haystack, (bool) $strict);
    }

    
    public static function iteratorToArray($iterator, $recursive = true)
    {
        
        if (! is_array($iterator) && ! $iterator instanceof Traversable) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable object');
        }

        if (! $recursive) {
            if (is_array($iterator)) {
                return $iterator;
            }

            return iterator_to_array($iterator);
        }

        if (
            is_object($iterator)
            && ! $iterator instanceof Iterator
            && method_exists($iterator, 'toArray')
        ) {
            
            $array = $iterator->toArray();

            return $array;
        }

        $array = [];
        foreach ($iterator as $key => $value) {
            if (is_scalar($value)) {
                $array[$key] = $value;
                continue;
            }

            if ($value instanceof Traversable) {
                $array[$key] = static::iteratorToArray($value, $recursive);
                continue;
            }

            if (is_array($value)) {
                $array[$key] = static::iteratorToArray($value, $recursive);
                continue;
            }

            $array[$key] = $value;
        }

        

        return $array;
    }

    
    public static function merge(array $a, array $b, $preserveNumericKeys = false)
    {
        foreach ($b as $key => $value) {
            if ($value instanceof MergeReplaceKeyInterface) {
                $a[$key] = $value->getData();
            } elseif (isset($a[$key]) || array_key_exists($key, $a)) {
                if ($value instanceof MergeRemoveKey) {
                    unset($a[$key]);
                } elseif (! $preserveNumericKeys && is_int($key)) {
                    $a[] = $value;
                } elseif (is_array($value) && is_array($a[$key])) {
                    $a[$key] = static::merge($a[$key], $value, $preserveNumericKeys);
                } else {
                    $a[$key] = $value;
                }
            } else {
                if (! $value instanceof MergeRemoveKey) {
                    $a[$key] = $value;
                }
            }
        }

        return $a;
    }

    
    public static function filter(array $data, $callback, $flag = null)
    {
        if (! is_callable($callback)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Second parameter of %s must be callable',
                __METHOD__
            ));
        }

        return array_filter($data, $callback, $flag ?? 0);
    }
}
