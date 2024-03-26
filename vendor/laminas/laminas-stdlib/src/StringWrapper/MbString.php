<?php

declare(strict_types=1);

namespace Laminas\Stdlib\StringWrapper;

use Laminas\Stdlib\Exception;

use function array_map;
use function array_search;
use function extension_loaded;
use function mb_convert_encoding;
use function mb_list_encodings;
use function mb_strlen;
use function mb_strpos;
use function mb_substr;

class MbString extends AbstractStringWrapper
{
    
    protected static $encodings;

    
    public static function getSupportedEncodings()
    {
        if (static::$encodings === null) {
            static::$encodings = array_map('strtoupper', mb_list_encodings());

            
            $indexIso885916 = array_search('ISO-8859-16', static::$encodings, true);
            if ($indexIso885916 !== false) {
                unset(static::$encodings[$indexIso885916]);
            }
        }

        return static::$encodings;
    }

    
    public function __construct()
    {
        if (! extension_loaded('mbstring')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "mbstring" is required for this wrapper'
            );
        }
    }

    
    public function strlen($str)
    {
        return mb_strlen($str, $this->getEncoding());
    }

    
    public function substr($str, $offset = 0, $length = null)
    {
        return mb_substr($str, $offset, $length, $this->getEncoding());
    }

    
    public function strpos($haystack, $needle, $offset = 0)
    {
        return mb_strpos($haystack, $needle, $offset, $this->getEncoding());
    }

    
    public function convert($str, $reverse = false)
    {
        $encoding        = $this->getEncoding();
        $convertEncoding = $this->getConvertEncoding();

        if ($convertEncoding === null) {
            throw new Exception\LogicException(
                'No convert encoding defined'
            );
        }

        if ($encoding === $convertEncoding) {
            return $str;
        }

        $fromEncoding = $reverse ? $convertEncoding : $encoding;
        $toEncoding   = $reverse ? $encoding : $convertEncoding;

        return mb_convert_encoding($str, $toEncoding ?? '', $fromEncoding ?? '');
    }
}
