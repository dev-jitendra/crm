<?php


namespace Espo\Core\Utils;

use Exception;
use RuntimeException;
use stdClass;

class Util
{
    
    protected static $separator = DIRECTORY_SEPARATOR;
    
    protected static $reservedWordList = ['Case'];

    
    public static function getSeparator(): string
    {
        return static::$separator;
    }

    
    public static function camelCaseToUnderscore(string $string): string
    {
        return static::toUnderScore($string);
    }

    
    public static function hyphenToCamelCase(string $string): string
    {
        return self::toCamelCase($string, '-');
    }

    
    public static function camelCaseToHyphen(string $string): string
    {
        return static::fromCamelCase($string, '-');
    }

    
    public static function toFormat(string $name, string $delimiter = '/'): string
    {
        
        return preg_replace("/[\/\\\]/", $delimiter, $name);
    }

    
    public static function toCamelCase($input, string $symbol = '_', bool $capitaliseFirstChar = false)
    {
        if (is_array($input)) { 
            foreach ($input as &$value) {
                $value = static::toCamelCase($value, $symbol, $capitaliseFirstChar);
            }

            return $input; 
        }

        $input = lcfirst($input);

        if ($capitaliseFirstChar) {
            $input = ucfirst($input);
        }

        
        return preg_replace_callback(
            '/' . $symbol . '([a-zA-Z])/',
            
            function ($matches): string {
                return strtoupper($matches[1]);
            },
            $input
        );
    }

    
    public static function fromCamelCase($input, string $symbol = '_')
    {
        if (is_array($input)) { 
            foreach ($input as &$value) {
                $value = static::fromCamelCase($value, $symbol);
            }

            return $input; 
        }

        $input[0] = strtolower($input[0]);

        
        return preg_replace_callback(
            '/([A-Z])/',
            function ($matches) use ($symbol) {
                return $symbol . strtolower($matches[1]);
            },
            $input
        );
    }

    
    public static function toUnderScore($input)
    {
        return static::fromCamelCase($input, '_');
    }

    
    public static function merge($currentArray, $newArray)
    {
        
        

        $mergeIdentifier = '__APPEND__';

        if (is_array($currentArray) && !is_array($newArray)) {
            return $currentArray;
        }
        else if (!is_array($currentArray) && is_array($newArray)) {
            return $newArray;
        }
        else if (
            (!is_array($currentArray) || empty($currentArray)) &&
            (!is_array($newArray) || empty($newArray))
        ) {
            return [];
        }

        foreach ($newArray as $newName => $newValue) {
            if (
                is_array($newValue) &&
                array_key_exists($newName, $currentArray) &&
                is_array($currentArray[$newName])
            ) {

                
                $appendKey = array_search($mergeIdentifier, $newValue, true);

                if ($appendKey !== false) {
                    unset($newValue[$appendKey]);

                    $newValue = array_merge($currentArray[$newName], $newValue);
                }
                else if (
                    !static::isSingleArray($newValue) ||
                    !static::isSingleArray($currentArray[$newName])
                ) {
                    $newValue = static::merge($currentArray[$newName], $newValue);
                }

            }

            
            if (!isset($currentArray[$newName]) && is_array($newValue)) {
                $newValue = static::unsetInArrayByValue($mergeIdentifier, $newValue);
            }

            $currentArray[$newName] = $newValue;
        }

        return $currentArray;
    }

    
    public static function unsetInArrayByValue($needle, array $haystack, bool $reIndex = true): array
    {
        $doReindex = false;

        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $haystack[$key] = static::unsetInArrayByValue($needle, $value);
            }
            else if ($needle === $value) {
                unset($haystack[$key]);

                if ($reIndex) {
                    $doReindex = true;
                }
            }
        }

        if ($doReindex) {
            $haystack = array_values($haystack);
        }

        return $haystack;
    }

    
    public static function concatPath($folderPath, ?string $filePath = null): string
    {
        if (is_array($folderPath)) {
            $fullPath = '';

            foreach ($folderPath as $path) {
                $fullPath = static::concatPath($fullPath, $path);
            }

            return static::fixPath($fullPath);
        }

        if (empty($filePath)) {
            return static::fixPath($folderPath);
        }

        if (empty($folderPath)) {
            return static::fixPath($filePath);
        }

        if (substr($folderPath, -1) == static::getSeparator() || str_ends_with($folderPath, '/')) {
            return static::fixPath($folderPath . $filePath);
        }

        return static::fixPath($folderPath) . static::getSeparator() . $filePath;
    }

    
    public static function fixPath(string $path): string
    {
        return str_replace('/', static::getSeparator(), $path);
    }

    
    public static function arrayToObject($array)
    {
        
        return self::arrayToObjectInternal($array);
    }

    
    private static function arrayToObjectInternal($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        
        $isList = $value === array_values($value);

        $value =  array_map(fn($v) => self::arrayToObjectInternal($v), $value);

        if (!$isList) {
            $value = (object) $value;
        }

        return $value;
    }

    
    public static function objectToArray($object)
    {
        
        return self::objectToArrayInternal($object);
    }

    
    private static function objectToArrayInternal($value)
    {
        if (is_object($value)) {
            $value = (array) $value;
        }

        if (is_array($value)) {
            return array_map(fn($v) => self::objectToArrayInternal($v), $value);
        }

        return $value;
    }

    
    public static function normalizeClassName($name)
    {
        if (in_array($name, self::$reservedWordList)) {
            $name .= 'Obj';
        }

        return $name;
    }

    
    public static function normalizeScopeName($name)
    {
        foreach (self::$reservedWordList as $reservedWord) {
            if ($reservedWord.'Obj' == $name) {
                return $reservedWord;
            }
        }

        return $name;
    }

    
    public static function getNaming(
        string $name,
        string $prePostFix,
        string $type = 'prefix',
        string $symbol = '_'
    ): ?string {

        if ($type == 'prefix') {
            return static::toCamelCase($prePostFix.$symbol.$name, $symbol);
        }

        if ($type == 'postfix') {
            return static::toCamelCase($name.$symbol.$prePostFix, $symbol);
        }

        return null;
    }

    
    public static function replaceInArray($search = '', $replace = '', $array = [], $isKeys = true)
    {
        if (!is_array($array)) {
            return str_replace($search, $replace, $array);
        }

        $newArr = [];

        foreach ($array as $key => $value) {
            $addKey = $key;

            if ($isKeys) {
                $addKey = str_replace($search, $replace, $key);
            }

            $newArr[$addKey] = static::replaceInArray($search, $replace, $value, $isKeys);
        }

        return $newArr;
    }

    
    public static function unsetInArray(array $content, $unsets, bool $unsetParentEmptyArray = false)
    {
        if (empty($unsets)) {
            return $content;
        }

        if (is_string($unsets)) {
            $unsets = (array) $unsets;
        }

        foreach ($unsets as $rootKey => $unsetItem) {
            $unsetItem = is_array($unsetItem) ? $unsetItem : (array) $unsetItem;

            foreach ($unsetItem as $unsetString) {
                if (is_string($rootKey)) {
                    $unsetString = $rootKey . '.' . $unsetString;
                }

                $keyArr = explode('.', $unsetString);
                $keyChainCount = count($keyArr) - 1;

                $elem = &$content;

                $elementArr = [];
                $elementArr[] = &$elem;

                for ($i = 0; $i <= $keyChainCount; $i++) {
                    if (is_array($elem) && array_key_exists($keyArr[$i], $elem)) {
                        if ($i == $keyChainCount) {
                            unset($elem[$keyArr[$i]]);

                            if ($unsetParentEmptyArray) {
                                for ($j = count($elementArr); $j > 0; $j--) {
                                    $pointer =& $elementArr[$j];

                                    if (is_array($pointer) && empty($pointer)) {
                                        $previous =& $elementArr[$j - 1];
                                        unset($previous[$keyArr[$j - 1]]);
                                    }
                                }
                            }
                        } else if (is_array($elem[$keyArr[$i]])) {
                            $elem = &$elem[$keyArr[$i]];

                            $elementArr[] = &$elem;
                        }

                    }
                }
            }
        }

        return $content;
    }


    
    public static function getClassName(string $filePath): string
    {
        
        $className = preg_replace('/\.php$/i', '', $filePath);
        
        $className = preg_replace('/^(application|custom)(\/|\\\)/i', '', $className);
        
        return static::toFormat($className, '\\');
    }

    
    public static function getValueByKey($data, $key = null, $default = null)
    {
        if (empty($key)) {
            return $data;
        }

        if (is_array($key)) {
            $keys = $key;
        }
        else {
            $keys = explode('.', $key);
        }

        $item = $data;

        foreach ($keys as $keyName) {
            if (is_array($item)) {
                if (isset($item[$keyName])) {
                    $item = $item[$keyName];
                }
                else {
                    return $default;
                }
            }
            else if (is_object($item)) {
                if (isset($item->$keyName)) {
                    $item = $item->$keyName;
                }
                else {
                    return $default;
                }
            }
        }

        return $item;
    }

    
    public static function areEqual($var1, $var2): bool
    {
        if (is_array($var1)) {
            static::ksortRecursive($var1);
        }

        if (is_array($var2)) {
            static::ksortRecursive($var2);
        }

        return ($var1 === $var2);
    }

    
    public static function ksortRecursive(&$array): bool
    {
        if (!is_array($array)) {
            return false;
        }

        ksort($array);

        foreach ($array as $key => $value) {
            static::ksortRecursive($array[$key]);
        }

        return true;
    }

    
    public static function isSingleArray(array $array): bool
    {
        foreach ($array as $key => $value) {
            if (!is_int($key)) {
                return false;
            }
        }

        return true;
    }

    
    public static function generateUuid4(): string
    {
        try {
            $data = random_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

            $hex = bin2hex($data);
        }
        catch (Exception) {
            throw new RuntimeException("Could not generate UUID.");
        }

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($hex, 4));
    }

    
    public static function generateId(): string
    {
        return uniqid() . substr(md5((string) rand()), 0, 4);
    }

    
    public static function generateMoreEntropyId(): string
    {
        return
            substr(md5(uniqid((string) rand(), true)), 0, 16) .
            substr(md5((string) rand()), 0, 4);
    }

    
    public static function generateCryptId(): string
    {
        if (!function_exists('random_bytes')) {
            return self::generateMoreEntropyId();
        }

        return bin2hex(random_bytes(16));
    }

    
    public static function generateApiKey(): string
    {
        return self::generateCryptId();
    }

    
    public static function generateSecretKey(): string
    {
        return self::generateCryptId();
    }

    
    public static function generateKey(): string
    {
        return md5(uniqid((string) rand(), true));
    }

    
    public static function sanitizeFileName(string $fileName): string
    {
        
        return preg_replace("/([^\w\s\d\-_~,;:\[\]\(\).])/u", '_', $fileName);
    }

    
    public static function arrayDiff(array $array1, array $array2)
    {
        $diff = [];

        foreach ($array1 as $key1 => $value1) {
            if (array_key_exists($key1, $array2)) {
                if ($value1 !== $array2[$key1]) {
                    $diff[$key1] = $array2[$key1];
                }

                continue;
            }

            $diff[$key1] = $value1;
        }

        return array_merge($diff, array_diff_key($array2, $array1));
    }

    
    public static function fillArrayKeys($keys, $value)
    {
        $arrayKeys = is_array($keys) ? $keys : explode('.', $keys);

        $array = [];

        foreach (array_reverse($arrayKeys) as $i => $key) {
            $array = [
                $key => ($i == 0) ? $value : $array,
            ];
        }

        return $array;
    }

    
    public static function arrayKeysExists(array $keys, array $array)
    {
       return !array_diff_key(array_flip($keys), $array);
    }

    
    public static function convertToByte(string $value): int
    {
        $valueTrimmed = trim($value);
        $last = strtoupper(substr($valueTrimmed, -1));

        return match ($last) {
            'G' => (int) $valueTrimmed * 1024 * 1024 * 1024 ,
            'M' => (int) $valueTrimmed * 1024 * 1024,
            'K' => (int) $valueTrimmed * 1024,
            default => (int) $valueTrimmed,
        };
    }

    
    public static function areValuesEqual($v1, $v2, bool $isUnordered = false): bool
    {
        if (is_array($v1) && is_array($v2)) {
            if ($isUnordered) {
                sort($v1);
                sort($v2);
            }

            if ($v1 != $v2) {
                return false;
            }

            foreach ($v1 as $i => $itemValue) {
                if (is_object($itemValue) && is_object($v2[$i])) {
                    if (!self::areValuesEqual($itemValue, $v2[$i])) {
                        return false;
                    }

                    continue;
                }

                if ($itemValue !== $v2[$i]) {
                    return false;
                }
            }

            return true;
        }

        if (is_object($v1) && is_object($v2)) {
            if ($v1 != $v2) {
                return false;
            }

            $a1 = get_object_vars($v1);
            $a2 = get_object_vars($v2);

            foreach ($a1 as $key => $itemValue) {
                if (is_object($itemValue) && is_object($a2[$key])) {
                    if (!self::areValuesEqual($itemValue, $a2[$key])) {
                        return false;
                    }

                    continue;
                }

                if (is_array($itemValue) && is_array($a2[$key])) {
                    if (!self::areValuesEqual($itemValue, $a2[$key])) {
                        return false;
                    }

                    continue;
                }

                if ($itemValue !== $a2[$key]) {
                    return false;
                }
            }

            return true;
        }

        return $v1 === $v2;
    }

    
    public static function mbUpperCaseFirst(string $string): string
    {
        if (!$string) {
            return $string;
        }

        $length = mb_strlen($string);
        $firstChar = mb_substr($string, 0, 1);
        $then = mb_substr($string, 1, $length - 1);

        return mb_strtoupper($firstChar) . $then;
    }

    
    public static function mbLowerCaseFirst(string $string): string
    {
        if (!$string) {
            return $string;
        }

        $length = mb_strlen($string);
        $firstChar = mb_substr($string, 0, 1);
        $then = mb_substr($string, 1, $length - 1);

        return mb_strtolower($firstChar) . $then;
    }

    
    public static function sanitizeHtml($text, $permittedHtmlTags = ['p', 'br', 'b', 'strong', 'pre'])
    {
        if (is_array($text)) {
            foreach ($text as $key => &$value) {
                $value = self::sanitizeHtml($value, $permittedHtmlTags);
            }

            return $text;
        }

        $sanitized = htmlspecialchars($text, \ENT_QUOTES | \ENT_HTML5, 'UTF-8');

        foreach ($permittedHtmlTags as $htmlTag) {
            
            $sanitized = preg_replace('/&lt;(\/)?(' . $htmlTag . ')&gt;/i', '<$1$2>', $sanitized);
        }

        return $sanitized;
    }

    
    public static function urlAddParam(string $url, string $paramName, $paramValue): string
    {
        $urlQuery = parse_url($url, \PHP_URL_QUERY);

        if (!$urlQuery) {
            $params = [
                $paramName => $paramValue
            ];

            $url = trim($url);
            
            $url = preg_replace('/\/\?$/', '', $url);
            
            $url = preg_replace('/\/$/', '', $url);

            return $url . '/?' . http_build_query($params);
        }

        parse_str($urlQuery, $params);

        if (!isset($params[$paramName]) || $params[$paramName] != $paramValue) {
            $params[$paramName] = $paramValue;

            return str_replace($urlQuery, http_build_query($params), $url);
        }

        return $url;
    }

    
    public static function urlRemoveParam(string $url, string $paramName, string $suffix = ''): string
    {
        $urlQuery = parse_url($url, \PHP_URL_QUERY);

        if ($urlQuery) {
            parse_str($urlQuery, $params);

            if (isset($params[$paramName])) {
                unset($params[$paramName]);

                $newUrl = str_replace($urlQuery, http_build_query($params), $url);

                if (empty($params)) {
                    
                    $newUrl = preg_replace('/\/\?$/', '', $newUrl);
                    
                    $newUrl = preg_replace('/\/$/', '', $newUrl);

                    $newUrl .= $suffix;
                }

                return $newUrl;
            }
        }

        return $url;
    }

    
    public static function generatePassword(
        int $length = 8,
        int $letters = 5,
        int $digits = 3,
        bool $bothCases = false
    ): string {

        $chars = [
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
            '0123456789',
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'abcdefghijklmnopqrstuvwxyz',
        ];

        $shuffle = function ($array) {
            $currentIndex = count($array);

            while (0 !== $currentIndex) {
                $rand = (0 + (1 - 0) * (mt_rand() / mt_getrandmax()));
                $randomIndex = intval(floor($rand * $currentIndex));
                $currentIndex -= 1;
                $temporaryValue = $array[$currentIndex];

                $array[$currentIndex] = $array[$randomIndex];
                $array[$randomIndex] = $temporaryValue;
            }

            return $array;
        };

        $upperCase = 0;
        $lowerCase = 0;

        if ($bothCases) {
            $upperCase = 1;
            $lowerCase = 1;

            if ($letters >= 2) {
                $letters = $letters - 2;
            } else {
                $letters = 0;
            }
        }

        $either = $length - ($letters + $digits + $upperCase + $lowerCase);

        if ($either < 0) {
            $either = 0;
        }

        $array = [];

        foreach ([$letters, $digits, $either, $upperCase, $lowerCase] as $i => $len) {
            $set = $chars[$i];
            $subArray = [];

            $j = 0;

            while ($j < $len) {
                $rand = (0 + (1 - 0) * (mt_rand() / mt_getrandmax()));
                $index = intval(floor($rand * strlen($set)));
                $subArray[] = $set[$index];
                $j++;
            }

            $array = array_merge($array, $subArray);
        }

        return implode('', $shuffle($array));
    }

    
    public static function normilizeScopeName($name)
    {
        return self::normalizeScopeName($name);
    }

    
    public static function normilizeClassName($name)
    {
        return self::normalizeClassName($name);
    }
}
