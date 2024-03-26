<?php

declare(strict_types=1);

namespace Laminas\Stdlib\StringWrapper;

use Laminas\Stdlib\Exception;

use function extension_loaded;
use function grapheme_strlen;
use function grapheme_strpos;
use function grapheme_substr;

class Intl extends AbstractStringWrapper
{
    
    protected static $encodings = ['UTF-8'];

    
    public static function getSupportedEncodings()
    {
        return static::$encodings;
    }

    
    public function __construct()
    {
        if (! extension_loaded('intl')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "intl" is required for this wrapper'
            );
        }
    }

    
    public function strlen($str)
    {
        $len = grapheme_strlen($str);
        return $len ?? false;
    }

    
    public function substr($str, $offset = 0, $length = null)
    {
        
        if ($length !== null) {
            return grapheme_substr($str, $offset, $length);
        }

        return grapheme_substr($str, $offset);
    }

    
    public function strpos($haystack, $needle, $offset = 0)
    {
        return grapheme_strpos($haystack, $needle, $offset);
    }
}
